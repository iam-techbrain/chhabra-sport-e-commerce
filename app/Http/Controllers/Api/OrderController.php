<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->orderBy('id', 'desc');

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

        $order = Order::create([
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
            'items' => $validated['items'],
        ]);

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
