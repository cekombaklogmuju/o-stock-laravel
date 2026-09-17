<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Konsumen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'konsumens';

    protected $fillable = [
        'id_cabang',
        'nama',
        'slug',
        'alamat',
        'kota',
        'no_telp',
    ];

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_konsumen');
    }
}
