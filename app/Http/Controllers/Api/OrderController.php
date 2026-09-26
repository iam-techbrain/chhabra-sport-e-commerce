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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
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
     * Helper to recalculate cart subtotal and total from DB product prices
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

    public function index(Request $request)
    {
        $query = Order::query()->orderBy('id', 'desc');

        if ($request->has('email') && !empty($request->email)) {
            $email = trim($request->email);
            $query->where('customer_email', 'like', "%{$email}%");
        } elseif ($request->has('phone') && !empty($request->phone)) {
            $phone = trim($request->phone);
            $query->where('customer_phone', 'like', "%{$phone}%");
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Confirmed,Processing,Shipped,Dispatched,Delivered,Cancelled',
            'notes' => 'nullable|string',
            'dispatch_warehouse' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validated['status'];
        if ($request->has('notes')) {
            $order->notes = $request->notes;
        }
        if ($request->has('dispatch_warehouse')) {
            $order->dispatch_warehouse = $request->dispatch_warehouse;
        }
        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} status updated to {$order->status}!",
            'order' => $order
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'discount' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'items' => 'required|array|min:1',
        ]);

        try {
            // Recalculate price from DB and validate stock
            $calculation = $this->recalculateCartAndValidateStock(
                $validated['items'],
                (float) ($validated['discount'] ?? 0)
            );

            // Execute order creation & stock deduction atomically inside DB::transaction
            $order = DB::transaction(function () use ($validated, $request, $calculation) {
                $orderNumber = $this->generateOrderNumber();

                // Format point-by-point notes
                $notesArr = [];
                if ($request->filled('customer_notes')) {
                    $notesArr[] = '📌 Customer Note: ' . trim($request->customer_notes);
                }
                if ($request->filled('notes')) {
                    $notesArr[] = '📌 ' . trim($request->notes);
                }
                if (empty($notesArr)) {
                    $notesArr[] = '📌 Cash on Delivery (COD) Order Verified';
                }
                $notesStr = implode("\n", $notesArr);

                // Find associated user by user_id, email or phone
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

                return Order::create([
                    'user_id' => $userId,
                    'order_number' => $orderNumber,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'address' => $validated['address'],
                    'payment_method' => $validated['payment_method'],
                    'subtotal' => $calculation['subtotal'],
                    'discount' => $calculation['discount'],
                    'shipping' => $validated['shipping'] ?? 0,
                    'total' => $calculation['total'],
                    'status' => 'Confirmed',
                    'notes' => $notesStr,
                    'items' => $calculation['items'],
                ]);
            });

            // Send Email Notifications with Admin Receiver Email Sync Fallback
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
                Log::error("Order Email Notification Failed: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order' => $order,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Order Creation Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Error placing order.',
            ], 400);
        }
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}
