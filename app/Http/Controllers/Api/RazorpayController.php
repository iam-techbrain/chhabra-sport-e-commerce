<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderConfirmation;
use App\Mail\NewOrderAdminNotification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RazorpayController extends Controller
{
    /**
     * Helper to generate a collision-proof order number.
     * Format: CHS-YYYYMMDD-XXXX
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'CHS-' . date('Ymd') . '-';
        do {
            $random = strtoupper(Str::random(4));
            $orderNumber = $prefix . $random;
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Helper to recalculate cart total from Database product prices
     * and validate stock availability.
     */
    private function recalculateCartAndValidateStock(array $items, float $discount = 0.0): array
    {
        $calculatedSubtotal = 0.0;
        $verifiedItems = [];

        foreach ($items as $item) {
            $productId = $item['id'] ?? $item['product_id'] ?? null;
            $codeId = $item['code_id'] ?? null;
            $itemName = $item['name'] ?? 'Product';
            $qty = max(1, (int) ($item['qty'] ?? $item['quantity'] ?? 1));

            $product = null;
            if ($productId) {
                $product = Product::find($productId);
            }
            if (!$product && $codeId) {
                $product = Product::where('code_id', $codeId)->first();
            }
            if (!$product && $itemName) {
                $product = Product::where('name', trim($itemName))->first();
            }

            if (!$product) {
                throw new \Exception("Product '{$itemName}' was not found in store catalog.");
            }

            if (!$product->in_stock || ($product->stock !== null && $product->stock < $qty)) {
                $available = $product->stock ?? 0;
                throw new \Exception("Product '{$product->name}' is out of stock or has insufficient quantity (Available: {$available}).");
            }

            $unitPrice = (float) $product->price;
            $itemTotal = $unitPrice * $qty;
            $calculatedSubtotal += $itemTotal;

            $verifiedItems[] = array_merge($item, [
                'id' => $product->id,
                'code_id' => $product->code_id,
                'name' => $product->name,
                'price' => $unitPrice,
                'qty' => $qty,
                'total' => $itemTotal
            ]);
        }

        $cleanDiscount = max(0.0, (float) $discount);
        $calculatedTotal = max(0.0, $calculatedSubtotal - $cleanDiscount);

        return [
            'subtotal' => round($calculatedSubtotal, 2),
            'discount' => round($cleanDiscount, 2),
            'total' => round($calculatedTotal, 2),
            'items' => $verifiedItems
        ];
    }

    /**
     * Create a Razorpay Order ID on Razorpay Servers with Backend Price Verification
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'discount' => 'nullable|numeric',
        ]);

        $keyId = config('services.razorpay.key_id') ?: env('RAZORPAY_KEY_ID');
        $keySecret = config('services.razorpay.key_secret') ?: env('RAZORPAY_KEY_SECRET');

        if (empty($keyId) || empty($keySecret)) {
            Log::error('Razorpay API keys are not configured in environment.');
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway key configuration is missing.',
            ], 500);
        }

        try {
            // Recalculate order total from DB product prices to prevent price manipulation attack
            $calculation = $this->recalculateCartAndValidateStock(
                $request->input('items', []),
                (float) $request->input('discount', 0)
            );

            $calculatedTotal = $calculation['total'];
            if ($calculatedTotal <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order total calculated.',
                ], 400);
            }

            $amountInPaise = (int) round($calculatedTotal * 100);

            $response = Http::withBasicAuth($keyId, $keySecret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => 'rcpt_' . time() . '_' . rand(1000, 9999),
                    'payment_capture' => 1,
                ]);

            if ($response->successful()) {
                $orderData = $response->json();
                return response()->json([
                    'success' => true,
                    'razorpay_order_id' => $orderData['id'],
                    'key_id' => $keyId,
                    'amount' => $amountInPaise,
                    'verified_total' => $calculatedTotal,
                    'currency' => 'INR',
                ]);
            } else {
                Log::error('Razorpay Order Creation Failed', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create Razorpay payment order: ' . ($response->json()['error']['description'] ?? 'Gateway Error'),
                ], 400);
            }
        } catch (\Throwable $e) {
            Log::error('Razorpay Order Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Server error connecting to payment gateway.',
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment Signature, Verify Paid Amount, and Create Order
     */
    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'discount' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'items' => 'required|array|min:1',
        ]);

        $keyId = config('services.razorpay.key_id') ?: env('RAZORPAY_KEY_ID');
        $keySecret = config('services.razorpay.key_secret') ?: env('RAZORPAY_KEY_SECRET');

        if (empty($keySecret)) {
            Log::error('Razorpay secret key missing during payment verification.');
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway configuration error.',
            ], 500);
        }

        // 1. Cryptographic Signature Verification
        $expectedSignature = hash_hmac('sha256', $validated['razorpay_order_id'] . '|' . $validated['razorpay_payment_id'], $keySecret);

        if (!hash_equals($expectedSignature, $validated['razorpay_signature'])) {
            Log::error('Razorpay Signature Verification Failed', [
                'expected' => $expectedSignature,
                'received' => $validated['razorpay_signature'],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed! Payment is not authentic.',
            ], 400);
        }

        try {
            // 2. Recalculate price from DB and validate stock
            $calculation = $this->recalculateCartAndValidateStock(
                $validated['items'],
                (float) ($validated['discount'] ?? 0)
            );

            $expectedTotal = $calculation['total'];
            $expectedPaise = (int) round($expectedTotal * 100);

            // 3. Query Razorpay API to verify actual paid amount == order total
            if (!empty($keyId)) {
                $paymentCheck = Http::withBasicAuth($keyId, $keySecret)
                    ->get("https://api.razorpay.com/v1/payments/{$validated['razorpay_payment_id']}");

                if ($paymentCheck->successful()) {
                    $paymentData = $paymentCheck->json();
                    $paidAmountPaise = (int) ($paymentData['amount'] ?? 0);
                    if ($paidAmountPaise !== $expectedPaise) {
                        Log::error('Razorpay Payment Amount Tampering Detected', [
                            'paid' => $paidAmountPaise,
                            'expected' => $expectedPaise
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Payment amount mismatch! Paid amount does not match calculated order total.',
                        ], 400);
                    }
                }
            }

            // 4. Create Order & Deduct Product Stock Atomically inside DB::transaction
            $order = DB::transaction(function () use ($validated, $request, $calculation) {
                $orderNumber = $this->generateOrderNumber();

                // Point-by-point order notes
                $notesArr = [];
                $notesArr[] = '📌 Razorpay Order ID: ' . $validated['razorpay_order_id'];
                if (!empty($request->customer_notes)) {
                    $notesArr[] = '📌 Customer Note: ' . trim($request->customer_notes);
                }
                if (!empty($request->notes)) {
                    $notesArr[] = '📌 ' . trim($request->notes);
                }
                $notesStr = implode("\n", $notesArr);

                // Find user
                $userId = $request->user_id ?? null;
                if (!$userId) {
                    $user = User::where('email', strtolower($validated['customer_email']))
                        ->orWhere('phone', $validated['customer_phone'])
                        ->first();
                    if ($user) {
                        $userId = $user->id;
                    }
                }

                // Deduct Product Stock Automatically
                foreach ($calculation['items'] as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);
                    if ($product) {
                        if ($product->stock !== null) {
                            $product->stock = max(0, $product->stock - $item['qty']);
                            if ($product->stock <= 0) {
                                $product->in_stock = false;
                            }
                        }
                        $product->save();
                    }
                }

                // Create Order Record
                return Order::create([
                    'user_id' => $userId,
                    'order_number' => $orderNumber,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'address' => $validated['address'],
                    'payment_method' => 'Razorpay Online (' . $validated['razorpay_payment_id'] . ')',
                    'subtotal' => $calculation['subtotal'],
                    'discount' => $calculation['discount'],
                    'shipping' => $validated['shipping'] ?? 0,
                    'total' => $calculation['total'],
                    'status' => 'Confirmed',
                    'notes' => $notesStr,
                    'items' => $calculation['items'],
                ]);
            });

            // 5. Trigger Email Notifications with Admin Email Sync Fallback
            try {
                $adminEmailSetting = config('mail.get_email') ?: env('GET_EMAIL');
                if (empty($adminEmailSetting)) {
                    $adminEmailSetting = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS', 'admin@chhabrasport.com');
                }
                $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailSetting)));
                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)->send(new NewOrderAdminNotification($order));
                }

                if (!empty($order->customer_email)) {
                    Mail::to($order->customer_email)->send(new CustomerOrderConfirmation($order));
                }
            } catch (\Throwable $e) {
                Log::error("Razorpay Order Email Notification Failed: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Razorpay payment verified & order placed successfully!',
                'order' => $order,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Razorpay Payment Verification Transaction Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Error processing payment verification and order saving.',
            ], 400);
        }
    }
}
