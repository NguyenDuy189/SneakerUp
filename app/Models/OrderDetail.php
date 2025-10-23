<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $guarded = [];
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // ✅ Định nghĩa mối quan hệ mà controller đang gọi
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // (Tùy chọn) Quan hệ ngắn tới sản phẩm gốc
    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id',          // Khóa chính ở bảng product_variants
            'id',          // Khóa chính ở bảng products
            'variant_id',  // Khóa ngoại ở bảng order_details
            'product_id'   // Khóa ngoại ở bảng product_variants
        );
    }
}
