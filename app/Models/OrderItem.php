<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 *
 * @package App\Models
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property float $total
 *
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];

    /**
     * Mối quan hệ: OrderItem thuộc về một Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mối quan hệ: OrderItem thuộc về một Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
