<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchRequestItem extends Model
{
    use HasFactory;

    protected $table = 'branch_request_items';

    protected $fillable = [
        'id_branch_request',
        'id_produk',
        'jumlah_diminta',
        'jumlah_disetujui',
    ];

    public function branchRequest()
    {
        return $this->belongsTo(BranchRequest::class, 'id_branch_request');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
