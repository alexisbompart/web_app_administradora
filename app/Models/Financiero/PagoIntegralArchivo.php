<?php

namespace App\Models\Financiero;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PagoIntegralArchivo extends Model
{
    protected $table = 'pagointegral_archivos';

    protected $fillable = [
        'banco_id', 'nombre_archivo', 'tipo_archivo', 'cantidad_pagos',
        'monto_total', 'estatus', 'generado_por', 'fecha_generado',
        'fecha_enviado', 'fecha_procesado', 'observaciones',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'fecha_generado' => 'datetime',
        'fecha_enviado' => 'datetime',
        'fecha_procesado' => 'datetime',
    ];

    public const ESTATUS_GENERADO = 'GE';
    public const ESTATUS_ENVIADO = 'EN';
    public const ESTATUS_EN_PROCESO = 'EP';
    public const ESTATUS_PROCESADO = 'PR';

    public const ESTATUS_LABELS = [
        'GE' => 'Generado',
        'EN' => 'Enviado',
        'EP' => 'En Proceso',
        'PR' => 'Procesado',
    ];

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function generadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generado_por');
    }

    public function pagos(): BelongsToMany
    {
        return $this->belongsToMany(PagoIntegral::class, 'pagointegral_archivo_pagos', 'archivo_id', 'pagointegral_id');
    }

    public function getEstatusLabelAttribute(): string
    {
        return self::ESTATUS_LABELS[$this->estatus] ?? $this->estatus;
    }
}
