<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id','code','fullname','address','phone','payment_method',
        'status','total_price','shipping_fee','provider_id','note'
    ];
    public function user()
    {
        // Nếu bảng orders có cột user_id (kết nối với bảng users)
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }
    public function payment() {
        return $this->hasOne(Payment::class);
    }
    public function provider() {
        return $this->belongsTo(DeliveryProvider::class, 'provider_id');
    }

}
