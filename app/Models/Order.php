<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'payment_method',
        'subtotal',
        'discount',
        'shipping',
        'total',
        'status',
        'notes',
        'dispatch_warehouse',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'float',
        'discount' => 'float',
        'shipping' => 'float',
        'total' => 'float',
    ];
}
