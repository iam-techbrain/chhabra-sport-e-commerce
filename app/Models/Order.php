<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'float',
        'discount' => 'float',
        'shipping' => 'float',
        'total' => 'float',
    ];
}
