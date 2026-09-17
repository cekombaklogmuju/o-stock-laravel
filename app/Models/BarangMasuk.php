<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang_masuks';

    protected $fillable = [
        'no_masuk',
        'tanggal',
        'id_supplier',
        'no_surat_jalan',
        'keterangan',
        'created_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(BarangMasukItem::class, 'id_barang_masuk');
    }
}
