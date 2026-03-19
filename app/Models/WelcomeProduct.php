<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeProduct extends Model
{
    protected $fillable = [
        'titulo',
        'slogan',
        'descripcion',
        'detalle',
        'icono',
        'color',
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
