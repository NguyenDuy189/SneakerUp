<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'price',
        'stock',
        'sku',
    ];

    /**
     * 🔗 Quan hệ: Biến thể thuộc về sản phẩm
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 🔗 Quan hệ: Màu sắc
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * 🔗 Quan hệ: Kích cỡ
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * 🔗 Quan hệ: Biến thể xuất hiện trong nhiều chi tiết đơn hàng
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'variant_id');
    }

    /**
     * ⚙️ Lấy tên hiển thị đầy đủ cho admin hoặc invoice
     */
    public function getDisplayNameAttribute()
    {
        $productName = $this->product->name ?? 'Sản phẩm không xác định';
        $color = $this->color->color_name ?? 'Không rõ màu';
        $size = $this->size->size_value ?? 'Không rõ size';

        return "{$productName} ({$color} - {$size})";
    }

    /**
     * ⚙️ Kiểm tra còn hàng không
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }
}
