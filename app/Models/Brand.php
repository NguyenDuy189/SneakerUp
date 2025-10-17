<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brands'; // bảng có thể khác, kiểm tra trong DB
    protected $fillable = ['name','status'];
}
