<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    // Tắt timestamps (created_at, updated_at) cho bảng này
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
