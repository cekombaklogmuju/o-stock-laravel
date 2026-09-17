<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_items';

    protected $fillable = [
        'id_stock_opname',
        'id_produk',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'alasan',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class, 'id_stock_opname');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
