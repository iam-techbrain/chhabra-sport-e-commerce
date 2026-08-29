<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrderConfirmation;
use App\Mail\NewOrderAdminNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class OrderController extends Controller
{
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
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
        ]);

        $orderNumber = 'CHS-' . rand(100000, 999999);

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
            'payment_method' => $validated['payment_method'],
            'subtotal' => $validated['subtotal'],
            'discount' => $validated['discount'] ?? 0,
            'shipping' => $validated['shipping'] ?? 0,
            'total' => $validated['total'],
            'status' => 'Confirmed',
            'notes' => $notesStr,
            'items' => $validated['items'],
        ]);



        // Send Email Notifications (Admin & Customer)
        try {
            $adminEmailsRaw = config('mail.get_email') ?: env('GET_EMAIL');
            if (!empty($adminEmailsRaw)) {
                // Support multiple comma-separated email addresses (e.g. "admin1@mail.com, admin2@mail.com")
                $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailsRaw)));
                if (!empty($adminEmails)) {
                    Mail::to($adminEmails)->send(new NewOrderAdminNotification($order));
                }
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
