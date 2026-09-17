<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlokasiItem extends Model
{
    use HasFactory;

    protected $table = 'alokasi_items';

    protected $fillable = [
        'id_alokasi',
        'id_produk',
        'jumlah',
    ];

    public function alokasi()
    {
        return $this->belongsTo(Alokasi::class, 'id_alokasi');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
