<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_category_id',
        'title',
        'slug',
        'content',
        'image',
        'status',
    ];

    /**
     * Định nghĩa quan hệ NGƯỢC: Một Bài viết (Post)
     * thuộc về (belongsTo) một Danh mục (PostCategory).
     */
    public function category()
    {
        // 'post_category_id' là khóa ngoại trong bảng 'posts'
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }
}
