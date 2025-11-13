<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $guarded = [];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Trả về label tiếng Việt cho trạng thái
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending'   => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'failed'    => 'Thất bại',
            'returned'  => 'Đã trả hàng',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Trả về màu badge bootstrap cho trạng thái
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending'   => 'warning',
            'confirmed' => 'info',
            'shipping'  => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'failed'    => 'secondary',
            'returned'  => 'dark',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Accessor tùy chọn: Tính tổng tiền sau giảm voucher (nếu cần hiển thị)
    public function getTotalAfterDiscountAttribute()
    {
        $total = $this->total_price;
        if ($this->voucher && $this->voucher->is_active && !$this->voucher->is_expired) {
            $discount = ($total * $this->voucher->discount / 100);
            if ($this->voucher->max_discount) {
                $discount = min($discount, $this->voucher->max_discount);
            }
            $total -= $discount;
        }
        return max($total, 0);  // Không âm
    }
}
