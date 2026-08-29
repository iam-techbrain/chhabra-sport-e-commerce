<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderConfirmation;
use App\Mail\NewOrderAdminNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RazorpayController extends Controller
{
    /**
     * Create a Razorpay Order ID on Razorpay Servers
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $keyId = config('services.razorpay.key_id') ?: env('RAZORPAY_KEY_ID', 'rzp_test_U8x8IJzoiGUV9Q');
        $keySecret = config('services.razorpay.key_secret') ?: env('RAZORPAY_KEY_SECRET', 'E0SuPWhDjPy4w6kAmibbmEAA');

        $amountInPaise = (int) round($validated['amount'] * 100);

        try {
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
                    'currency' => 'INR',
                ]);
            } else {
                Log::error('Razorpay Order Creation Failed', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create Razorpay payment order. ' . ($response->json()['error']['description'] ?? 'API Error'),
                ], 400);
            }
        } catch (\Throwable $e) {
            Log::error('Razorpay Order Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error connecting to Razorpay payment gateway.',
            ], 500);
        }
    }

    /**
     * Verify Razorpay Payment Signature and Create Order
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
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $keySecret = config('services.razorpay.key_secret') ?: env('RAZORPAY_KEY_SECRET', 'E0SuPWhDjPy4w6kAmibbmEAA');

        // Cryptographic Signature Verification
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

        // Signature is valid -> Create Order
        $orderNumber = 'CHS-' . rand(100000, 999999);

        // Format point-by-point notes
        $notesArr = [];
        $notesArr[] = '📌 Razorpay Order ID: ' . $validated['razorpay_order_id'];
        if (!empty($request->customer_notes)) {
            $notesArr[] = '📌 Customer Note: ' . trim($request->customer_notes);
        }
        if (!empty($request->notes)) {
            $notesArr[] = '📌 ' . trim($request->notes);
        }
        $notesStr = implode("\n", $notesArr);

        // Find associated user by user_id, email or phone
        $userId = $request->user_id ?? null;
        if (!$userId) {
            $user = \App\Models\User::where('email', strtolower($validated['customer_email']))
                ->orWhere('phone', $validated['customer_phone'])
                ->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'address' => $validated['address'],
            'payment_method' => 'Razorpay Online (' . $validated['razorpay_payment_id'] . ')',
            'subtotal' => $validated['subtotal'],
            'discount' => $validated['discount'] ?? 0,
            'shipping' => $validated['shipping'] ?? 0,
            'total' => $validated['total'],
            'status' => 'Confirmed',
            'notes' => $notesStr,
            'items' => $validated['items'],
        ]);



        // Trigger Email Notifications (Admin & Customer)
        try {
            $adminEmailsRaw = config('mail.get_email') ?: env('GET_EMAIL');
            if (!empty($adminEmailsRaw)) {
                $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailsRaw)));
                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)->send(new NewOrderAdminNotification($order));
                }
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
    }
}
