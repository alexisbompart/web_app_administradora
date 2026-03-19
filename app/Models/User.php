<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'telefono',
        'activo',
        'cod_compania',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    public function compania()
    {
        return $this->belongsTo(Condominio\Compania::class, 'cod_compania');
    }

    public function propietario()
    {
        return $this->hasOne(Condominio\Propietario::class);
    }

    public function trabajador()
    {
        return $this->hasOne(Personal\Trabajador::class);
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor\Proveedor::class);
    }
}
