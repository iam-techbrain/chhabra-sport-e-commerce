<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'user_name', 'rating', 'comment', 'is_verified', 'is_approved'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
