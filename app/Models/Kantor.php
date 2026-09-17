<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kantor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kantors';

    protected $fillable = [
        'kode_cabang',
        'tipe',
        'nama',
        'slug',
        'alamat',
    ];

    public function salesmen()
    {
        return $this->hasMany(Salesman::class, 'id_cabang');
    }

    public function konsumens()
    {
        return $this->hasMany(Konsumen::class, 'id_cabang');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_cabang');
    }

    public function alokasis()
    {
        return $this->hasMany(Alokasi::class, 'id_cabang');
    }

    public function kartuStoks()
    {
        return $this->hasMany(KartuStok::class, 'id_cabang');
    }
}
