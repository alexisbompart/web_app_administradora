<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';

    protected $fillable = [
        'nombre',
        'iso_code',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
