<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branch_requests';

    protected $fillable = [
        'no_permintaan',
        'tanggal_permintaan',
        'id_cabang_peminta',
        'id_user_peminta',
        'status',
        'prioritas',
        'keterangan',
        'tanggal_diproses',
        'diproses_oleh',
        'catatan_proses',
    ];

    public function cabangPeminta()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang_peminta');
    }

    public function userPeminta()
    {
        return $this->belongsTo(User::class, 'id_user_peminta');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function items()
    {
        return $this->hasMany(BranchRequestItem::class, 'id_branch_request');
    }
}
