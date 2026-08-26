<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeGuide extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'title', 'chart_json'];

    protected $casts = [
        'chart_json' => 'array',
    ];
}
