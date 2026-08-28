<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::count() > 0) return;

        Order::create([
            'order_number' => 'CHS-892101',
            'customer_name' => 'Amit Sharma',
            'customer_email' => 'amit.sharma@gmail.com',
            'customer_phone' => '+91 98112 34567',
            'address' => 'Flat 402, Royale Heights, MG Road, New Delhi, 110001',
            'payment_method' => 'UPI (GPay / PhonePe)',
            'subtotal' => 14999.00,
            'discount' => 500.00,
            'shipping' => 0.00,
            'total' => 14499.00,
            'status' => 'Confirmed',
            'items' => [
                [
                    'id' => 1,
                    'name' => 'Astrox 88D Pro Badminton Racquet',
                    'specs' => '4U / G5 · Pre-String Option',
                    'price' => 14999,
                    'quantity' => 1,
                    'img' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&q=80'
                ]
            ]
        ]);

        Order::create([
            'order_number' => 'CHS-892102',
            'customer_name' => 'Priya Patel',
            'customer_email' => 'priya.patel@yahoo.com',
            'customer_phone' => '+91 97234 56789',
            'address' => '12B Sector 15, Vasundhara, Noida, UP, 201301',
            'payment_method' => 'Cash on Delivery (COD)',
            'subtotal' => 18999.00,
            'discount' => 0.00,
            'shipping' => 150.00,
            'total' => 19149.00,
            'status' => 'Processing',
            'items' => [
                [
                    'id' => 2,
                    'name' => 'Head Speed MP 2024 Tennis Racquet',
                    'specs' => '300g · 100 sq.in · Auxetic 2.0 Tech',
                    'price' => 18999,
                    'quantity' => 1,
                    'img' => 'https://images.unsplash.com/photo-1617083934555-ac7d4fed8814?w=400&q=80'
                ]
            ]
        ]);

        Order::create([
            'order_number' => 'CHS-892103',
            'customer_name' => 'Vikram Singh',
            'customer_email' => 'vikram.singh@outlook.com',
            'customer_phone' => '+91 99887 11223',
            'address' => 'House 45, Model Town Phase 2, Ludhiana, Punjab, 141002',
            'payment_method' => 'Credit / Debit Card',
            'subtotal' => 24998.00,
            'discount' => 1000.00,
            'shipping' => 0.00,
            'total' => 23998.00,
            'status' => 'Shipped',
            'items' => [
                [
                    'id' => 3,
                    'name' => 'Ton Reserve Edition English Willow Bat',
                    'specs' => 'Grade 1 Willow · Weight 1180g',
                    'price' => 12499,
                    'quantity' => 2,
                    'img' => 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&q=80'
                ]
            ]
        ]);

        Order::create([
            'order_number' => 'CHS-892104',
            'customer_name' => 'Neha Gupta',
            'customer_email' => 'neha.gupta@hotmail.com',
            'customer_phone' => '+91 98765 43210',
            'address' => 'Flat 101, Sun City Apartments, Outer Ring Road, Bengaluru, 560103',
            'payment_method' => 'UPI (Paytm)',
            'subtotal' => 5499.00,
            'discount' => 200.00,
            'shipping' => 0.00,
            'total' => 5299.00,
            'status' => 'Delivered',
            'items' => [
                [
                    'id' => 5,
                    'name' => 'Gel-Rocket 11 Non-Marking Indoor Shoes',
                    'specs' => 'Gel Cushioning · Non-Marking Gum Sole',
                    'price' => 5499,
                    'quantity' => 1,
                    'img' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80'
                ]
            ]
        ]);
    }
}
