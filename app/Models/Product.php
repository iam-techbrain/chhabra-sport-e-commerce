<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code_id',
        'category',
        'brand',
        'name',
        'price',
        'old_price',
        'rating',
        'reviews',
        'tag',
        'specs',
        'img',
        'img2',
        'in_stock',
        'is_variable',
        'variations'
    ];

    protected $casts = [
        'price' => 'float',
        'old_price' => 'float',
        'rating' => 'float',
        'reviews' => 'integer',
        'in_stock' => 'boolean',
        'is_variable' => 'boolean',
        'variations' => 'array'
    ];
}
