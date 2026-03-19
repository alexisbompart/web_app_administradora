<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WelcomeService extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'icono',
        'color_icono',
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
