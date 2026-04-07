<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomePopup extends Model
{
    protected $fillable = [
        'titulo',
        'contenido',
        'imagen',
        'boton_texto',
        'boton_url',
        'icono',
        'color',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
