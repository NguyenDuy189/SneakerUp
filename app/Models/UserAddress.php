<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fullname',
        'phone',
        'address',
        'is_default',
    ];

    /**
     * Định nghĩa quan hệ:
     * Một địa chỉ chỉ thuộc về MỘT người dùng.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
