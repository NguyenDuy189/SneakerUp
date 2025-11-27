<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Giữ nguyên dòng này để báo cho Laravel biết không có cột 'updated_at'
    const UPDATED_AT = null;

    /**
     * THÊM 2 CỘT MỚI VÀO $fillable
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'status',
        'admin_reply', // <-- THÊM DÒNG NÀY
        'replied_at',  // <-- THÊM DÒNG NÀY
    ];

    // Quan hệ: Một đánh giá thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Một đánh giá thuộc về 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
