<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeResidence extends Model
{
    protected $fillable = [
        'nombre',
        'imagen',
        'ubicacion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }
}
