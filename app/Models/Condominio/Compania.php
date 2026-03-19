<?php

namespace App\Models\Condominio;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compania extends Model
{
    use SoftDeletes;

    protected $table = 'cond_companias';

    protected $fillable = [
        'cod_compania',
        'nombre',
        'rif',
        'direccion',
        'telefono',
        'email',
        'logo',
        'activo',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class);
    }

    public function agrupaciones(): HasMany
    {
        return $this->hasMany(Agrupacion::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cod_compania');
    }
}
