<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportDetail extends Model
{
    protected $fillable = ['export_id', 'variant_id', 'quantity', 'price'];

    public function export()
    {
        return $this->belongsTo(Export::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

