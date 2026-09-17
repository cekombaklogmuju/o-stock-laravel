<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alokasis';

    protected $fillable = [
        'id_cabang',
        'no_alokasi',
        'tanggal',
        'keterangan',
    ];

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }

    public function items()
    {
        return $this->hasMany(AlokasiItem::class, 'id_alokasi');
    }
}
