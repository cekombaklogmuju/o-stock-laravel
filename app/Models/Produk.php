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
        'lokasi_rak',
        'satuan',
        'nama',
        'slug',
        'harga_jual',
        'stok',
        'stok_minimum',
        'spesifikasi',
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

    public function barangMasukItems()
    {
        return $this->hasMany(BarangMasukItem::class, 'id_produk');
    }

    public function barangKeluarItems()
    {
        return $this->hasMany(BarangKeluarItem::class, 'id_produk');
    }

    public function stockOpnameItems()
    {
        return $this->hasMany(StockOpnameItem::class, 'id_produk');
    }

    public function isLowStock(): bool
    {
        return $this->stok <= ($this->stok_minimum ?? 5);
    }
}
