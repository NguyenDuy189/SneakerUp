<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = ['reason', 'total_price'];

    public function details()
    {
        return $this->hasMany(ExportDetail::class);
    }
}

