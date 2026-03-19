<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominaDetalle extends Model
{
    protected $table = 'nomina_detalles';

    protected $fillable = [
        'nomina_id',
        'trabajador_id',
        'dias_trabajados',
        'salario_base',
        'horas_extras',
        'bono_alimentacion',
        'bono_transporte',
        'otros_ingresos',
        'sso_empleado',
        'sso_patronal',
        'lph_empleado',
        'lph_patronal',
        'islr',
        'otros_descuentos',
        'total_asignaciones',
        'total_deducciones',
        'neto_pagar',
    ];

    protected $casts = [
        'dias_trabajados' => 'integer',
        'salario_base' => 'decimal:2',
        'horas_extras' => 'decimal:2',
        'bono_alimentacion' => 'decimal:2',
        'bono_transporte' => 'decimal:2',
        'otros_ingresos' => 'decimal:2',
        'sso_empleado' => 'decimal:2',
        'sso_patronal' => 'decimal:2',
        'lph_empleado' => 'decimal:2',
        'lph_patronal' => 'decimal:2',
        'islr' => 'decimal:2',
        'otros_descuentos' => 'decimal:2',
        'total_asignaciones' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'neto_pagar' => 'decimal:2',
    ];

    public function nomina(): BelongsTo
    {
        return $this->belongsTo(Nomina::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
