<?php

namespace App\Models\Personal;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestamoTrabajador extends Model
{
    protected $table = 'prestamos_trabajadores';

    protected $fillable = [
        'trabajador_id',
        'monto_prestamo',
        'monto_cuota',
        'cuotas_totales',
        'cuotas_pagadas',
        'saldo_pendiente',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_inicio_descuento',
        'tasa_interes',
        'estatus',
        'aprobado_por',
        'motivo',
    ];

    protected $casts = [
        'monto_prestamo' => 'decimal:2',
        'monto_cuota' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'tasa_interes' => 'decimal:2',
        'fecha_solicitud' => 'date',
        'fecha_aprobacion' => 'date',
        'fecha_inicio_descuento' => 'date',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
