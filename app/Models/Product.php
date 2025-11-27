<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','brand_id','description','price','stock','is_active'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    // Quan hệ: Một sản phẩm có nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // THÊM HÀM NÀY: Tự động tính toán điểm trung bình
    // (chỉ tính các đánh giá đã được 'approved')
   public function getAverageRatingAttribute()
{
    // SỬA TỪ 'approved' THÀNH 'visible'
    return $this->reviews()->where('status', 'visible')->avg('rating');
}
}
