<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produks';

    protected $fillable = [
        'id_kategori',
        'id_supplier',
        'kode_produk',
        'barcode',
        'nama',
        'slug',
        'harga_jual',
        'stok',
        'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'id_kategori');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function kartuStoks()
    {
        return $this->hasMany(KartuStok::class, 'id_produk');
    }

    public function alokasiItems()
    {
        return $this->hasMany(AlokasiItem::class, 'id_produk');
    }

    public function penjualanItems()
    {
        return $this->hasMany(PenjualanItem::class, 'id_produk');
    }

    public function branchRequestItems()
    {
        return $this->hasMany(BranchRequestItem::class, 'id_produk');
    }
}
