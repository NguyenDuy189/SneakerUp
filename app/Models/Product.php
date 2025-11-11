<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;

class Product extends Model
{
    protected $table = 'products';
    use HasFactory;
    

    protected $fillable = [
        'name', 'brand_id', 'category_id', 'price', 'quantity', 'status', 'description', 'image', 'discount_id', 'is_featured'
    ];

    protected $appends = ['final_price'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
    return $this->hasMany(ProductVariant::class);
    }

    public function tags()
    {
    return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }

    public function discount()
    {
    // Mỗi product thuộc về một discount (discount_id trong bảng products)
    return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

/**
 * Giá sau khi áp mã giảm (attribute)
 */
public function getFinalPriceAttribute()
    {
        $discount = $this->discount;

        // Không có discount hoặc discount không hợp lệ
        if (!$discount || !$discount->id) {
            return $this->price;
        }

        // Ngoài thời gian áp dụng
        if (
            ($discount->start_date && now()->lt($discount->start_date)) ||
            ($discount->end_date && now()->gt($discount->end_date))
        ) {
            return $this->price;
        }

        // Giảm theo phần trăm
        if (!empty($discount->percent)) {
            return round($this->price * (1 - $discount->percent / 100), 0);
        }

        // Giảm theo số tiền
        if (!empty($discount->amount)) {
            return max(0, $this->price - $discount->amount);
        }

        return $this->price;
    }

    //quản lý kho hàng
        public function imports()
    {
        return $this->hasManyThrough(ImportDetail::class, ProductVariant::class, 'product_id', 'variant_id', 'id', 'id');
    }
}