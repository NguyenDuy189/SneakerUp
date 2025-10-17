<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
// 1. Thêm dòng này
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    // 2. Thêm dòng này
    use HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'phone',
        'role', // Chúng ta vẫn giữ lại cột này cho vai trò cơ bản
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Định nghĩa mối quan hệ: Một User có nhiều Order.
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
      public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Chỉ log các thuộc tính này khi có thay đổi
            ->logOnly(['fullname', 'email', 'status'])
            // Ghi lại cả dữ liệu cũ và mới khi có thay đổi
            ->logOnlyDirty()
            // Không lưu log nếu chỉ có các thuộc tính này thay đổi
            ->dontSubmitEmptyLogs();
    }
}
