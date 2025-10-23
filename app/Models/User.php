<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'points', // ✅ thêm trường tích điểm KHÔNG ảnh hưởng tính năng khác
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer', // ✅ ép kiểu int để xử lý chính xác khi cộng/trừ điểm
        ];
    }

    // 🔹 Liên kết với đơn hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 🔹 Liên kết với thanh toán (nếu có)
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
