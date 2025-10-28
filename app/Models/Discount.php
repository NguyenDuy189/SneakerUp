<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percent',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $table = 'discounts'; // tên bảng phải khớp với DB

    public function products()
    {
    // Một discount áp dụng cho nhiều product
    return $this->hasMany(Product::class, 'discount_id', 'id');
    }
}
