<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarItem extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar_items';

    protected $fillable = [
        'id_barang_keluar',
        'id_produk',
        'jumlah',
        'catatan',
    ];

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
