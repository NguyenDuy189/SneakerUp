<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportDetail extends Model
{
    use HasFactory;

    protected $table = 'import_details';

    protected $fillable = ['import_id', 'variant_id', 'quantity', 'price'];

    public $timestamps = false; // nếu bảng không có created_at/updated_at

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
