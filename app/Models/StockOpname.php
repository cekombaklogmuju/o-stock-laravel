<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_opnames';

    protected $fillable = [
        'no_opname',
        'tanggal',
        'keterangan',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'id_stock_opname');
    }
}
