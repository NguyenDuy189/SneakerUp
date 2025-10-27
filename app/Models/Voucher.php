<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'name',
        'discount_type', // 'percentage' hoặc 'fixed'
        'discount_value', // Giá trị giảm (ví dụ: 10% hoặc 50000 VND)
        'min_order_value', // Giá trị đơn hàng tối thiểu để áp dụng
        'max_discount', // Giảm tối đa (cho percentage)
        'usage_limit', // Số lần sử dụng tối đa
        'used_count', // Số lần đã sử dụng
        'expiry_date', // Ngày hết hạn
        'is_active', // Trạng thái kích hoạt
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
    ];

    // Relationship với Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Accessor để hiển thị discount dễ đọc
    public function getDiscountDisplayAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return number_format($this->discount_value) . ' ₫';
    }

    // Kiểm tra voucher còn hợp lệ không
    public function isValid()
    {
        return $this->is_active &&
                ($this->expiry_date === null || $this->expiry_date->isFuture()) &&
                ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}