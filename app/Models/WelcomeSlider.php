<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeSlider extends Model
{
    protected $fillable = [
        'titulo',
        'subtitulo',
        'imagen',
        'boton_texto',
        'boton_url',
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
