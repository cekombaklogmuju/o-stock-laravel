<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KartuStok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kartu_stoks';

    protected $fillable = [
        'id_produk',
        'id_cabang',
        'tanggal',
        'no_bukti',
        'keterangan',
        'stok_masuk',
        'stok_keluar',
        'saldo_akhir',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }
}
