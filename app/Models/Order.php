<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * Định nghĩa mối quan hệ: Một Order có nhiều OrderDetail.
 */
public function details()
{
    return $this->hasMany(OrderDetail::class, 'order_id');
}
}
