<?php

namespace App\Models\Financiero;

use Illuminate\Database\Eloquent\Model;

class MaestroTransaccionCaja extends Model
{
    protected $table = 'cond_maestro_transaccion_caja';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo',
        'afecta_saldo',
        'activo',
    ];

    protected $casts = [
        'afecta_saldo' => 'boolean',
        'activo' => 'boolean',
    ];
}
