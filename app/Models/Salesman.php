<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salesman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'salesmen';

    protected $fillable = [
        'id_cabang',
        'nama',
        'slug',
        'no_telp',
    ];

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_salesman');
    }
}
