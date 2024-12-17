<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'image',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean'
    ];
} 