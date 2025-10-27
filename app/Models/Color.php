<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'color_name',    // Tên màu (VD: Đỏ, Xanh, Trắng)
        'color_code',    // Mã màu dạng HEX (VD: #FF0000)
    ];

    /**
     * 🔗 Quan hệ: Một màu có thể áp dụng cho nhiều biến thể sản phẩm
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'color_id');
    }

    /**
     * 🎨 Lấy tên hiển thị màu sắc kèm mã màu (cho admin UI)
     */
    public function getDisplayLabelAttribute()
    {
        $code = $this->color_code ?? '#000';
        return "{$this->color_name} ({$code})";
    }
}
