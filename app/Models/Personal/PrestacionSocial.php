<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestacionSocial extends Model
{
    protected $table = 'prestaciones_sociales';

    protected $fillable = [
        'trabajador_id',
        'anio',
        'trimestre',
        'dias_acumulados',
        'monto_acumulado',
        'intereses',
        'anticipos',
        'saldo',
        'fecha_calculo',
        'observaciones',
    ];

    protected $casts = [
        'monto_acumulado' => 'decimal:2',
        'intereses' => 'decimal:2',
        'anticipos' => 'decimal:2',
        'saldo' => 'decimal:2',
        'fecha_calculo' => 'date',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
