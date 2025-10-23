<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $table = 'sizes';

    protected $fillable = [
        'size_value',  // Giá trị size (VD: 38, 39, 40, 41)
        'description', // Mô tả thêm (VD: "Nam EU Size 42")
    ];

    /**
     * 🔗 Quan hệ: Một size có thể có nhiều biến thể sản phẩm
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }

    /**
     * ⚙️ Hiển thị label size thân thiện
     */
    public function getDisplayLabelAttribute()
    {
        return "Size {$this->size_value}";
    }
}
