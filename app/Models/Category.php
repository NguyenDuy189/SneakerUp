<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * Các trường được phép gán hàng loạt (Mass Assignment).
     * BẮT BUỘC CÓ 'thumbnail' và 'slug' ở đây.
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'thumbnail', // << RẤT QUAN TRỌNG
        'description',
        'status',
        'sort_order',
        'position',
    ];

    protected $appends = [
        'status_label',
        'product_count',
    ];

    /**
     * Tự động xử lý khi model được "booted".
     */
    protected static function booted()
    {
        // Tự động tạo sort_order khi tạo mới
        static::creating(function ($category) {
            if (is_null($category->sort_order)) {
                $maxOrder = self::where('parent_id', $category->parent_id)->max('sort_order');
                $category->sort_order = $maxOrder + 1;
            }
        });
    }

    // Quan hệ danh mục con (tải đệ quy)
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('children') // Tự động tải các cấp cháu
            ->withCount('products') // Đếm sản phẩm cho cấp con
            ->orderBy('sort_order');
    }

    // Quan hệ danh mục cha
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Quan hệ sản phẩm (Giả sử bạn có model Product)
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class); 
    }

    // Tối ưu đếm sản phẩm
    public function getProductCountAttribute(): int
    {
        if (isset($this->attributes['products_count'])) {
            return (int) $this->attributes['products_count'];
        }
        return $this->products()->count();
    }

    // Trạng thái hiển thị
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'active' ? 'Hiển thị' : 'Đã ẩn';
    }
}