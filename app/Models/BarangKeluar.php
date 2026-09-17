<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang_keluars';

    protected $fillable = [
        'no_keluar',
        'tanggal',
        'tujuan',
        'penerima',
        'keterangan',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(BarangKeluarItem::class, 'id_barang_keluar');
    }
}
