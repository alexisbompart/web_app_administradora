<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class TasaBcv extends Model
{
    protected $table = 'cond_tasas_bcv';

    protected $fillable = [
        'fecha',
        'moneda',
        'tasa',
        'fuente',
    ];

    protected $casts = [
        'fecha' => 'date',
        'tasa' => 'decimal:6',
    ];
}
