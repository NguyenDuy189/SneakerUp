<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'method',
        'status',
        'amount',
        'currency',
        'paid_at',
        'confirmer_id',
        'refund_reference',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // 🔹 Liên kết với đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 Nhân viên xác nhận (confirmer)
    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmer_id');
    }

    // 🔹 Label tiếng Việt cho trạng thái
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Đang chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
            'cancelled' => 'Đơn hàng bị hủy',
            'chargeback' => 'Khách kiện',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    // 🔹 Màu badge Bootstrap
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            'cancelled' => 'dark',
            'chargeback' => 'info',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // 🔹 Phương thức hiển thị tên phương thức thanh toán
    public function getMethodLabelAttribute(): string
    {
        $methods = [
            'COD' => 'Thanh toán khi nhận hàng',
            'Momo' => 'Momo',
            'VNPay' => 'VNPay',
            'PayPal' => 'PayPal',
        ];

        return $methods[$this->method] ?? $this->method;
    }

}
