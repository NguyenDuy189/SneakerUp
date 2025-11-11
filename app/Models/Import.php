<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;

    protected $table = 'imports';

    protected $fillable = ['supplier_id', 'total_price'];

    public $timestamps = true;

    public function details()
    {
        return $this->hasMany(ImportDetail::class);
    }
}
