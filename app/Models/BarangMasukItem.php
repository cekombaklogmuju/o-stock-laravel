<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukItem extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk_items';

    protected $fillable = [
        'id_barang_masuk',
        'id_produk',
        'jumlah',
        'catatan',
    ];

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_barang_masuk');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
