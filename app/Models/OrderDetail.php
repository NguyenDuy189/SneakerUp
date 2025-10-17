<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $guarded = []; // hoặc đặt $fillable làm an toàn

    public $timestamps = false; // nếu bảng order_details không có created_at/updated_at

    public function order() { return $this->belongsTo(Order::class); }
    public function variant() { return $this->belongsTo(ProductVariant::class,'variant_id'); }
}
