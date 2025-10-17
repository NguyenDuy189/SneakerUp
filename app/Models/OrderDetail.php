<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    // Bảng này không có timestamps (created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'variant_id',
        'product_name',
        'color_name',
        'size_value',
        'quantity',
        'price',
        'subtotal',
    ];
}
