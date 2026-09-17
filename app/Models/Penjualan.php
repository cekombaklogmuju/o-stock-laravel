<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penjualans';

    protected $fillable = [
        'no_invoice',
        'tanggal',
        'id_cabang',
        'id_konsumen',
        'id_salesman',
        'total_harga',
        'catatan',
    ];

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }

    public function konsumen()
    {
        return $this->belongsTo(Konsumen::class, 'id_konsumen');
    }

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'id_salesman');
    }

    public function items()
    {
        return $this->hasMany(PenjualanItem::class, 'id_penjualan');
    }
}
