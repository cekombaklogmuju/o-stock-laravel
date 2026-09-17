<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'id_cabang',
        'id_salesman',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cabang()
    {
        return $this->belongsTo(Kantor::class, 'id_cabang');
    }

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'id_salesman');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCabang(): bool
    {
        return $this->role === 'cabang';
    }

    public function isSalesman(): bool
    {
        return $this->role === 'salesman';
    }
}
