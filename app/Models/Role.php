<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
// THÊM CÁC DÒNG NÀY
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends SpatieRole
{
    use HasFactory, LogsActivity; // THÊM LogsActivity VÀO ĐÂY
 public const SUPER_ADMIN = 'Super Admin';
    // THÊM TOÀN BỘ HÀM NÀY VÀO
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Chỉ log thuộc tính 'name' khi có thay đổi
            ->logOnly(['name'])
            // Ghi lại cả dữ liệu cũ và mới khi có thay đổi
            ->logOnlyDirty()
            // Không lưu log nếu không có gì thay đổi
            ->dontSubmitEmptyLogs();
    }
}
