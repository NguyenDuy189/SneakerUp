<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'fullname',
        'address',
        'phone',
        'payment_method',
        'status',
        'total_price',
        'shipping_fee',
        'voucher_id',
        'provider_id',
    ];
   /**
     * Quan hệ: Một Đơn hàng (Order) có NHIỀU Chi tiết (OrderDetail)
     */
    public function details()
    {
        // Sẽ liên kết với bảng 'order_details' dựa trên 'order_id'
        return $this->hasMany(OrderDetail::class);
    }
}
