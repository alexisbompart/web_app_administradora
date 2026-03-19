<?php

namespace App\Models\Personal;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

    protected $fillable = [
        'trabajador_id',
        'periodo_desde',
        'periodo_hasta',
        'dias_correspondientes',
        'dias_disfrutados',
        'dias_pendientes',
        'fecha_salida',
        'fecha_reincorporacion',
        'suplente_id',
        'monto_bono_vacacional',
        'estatus',
        'aprobado_por',
    ];

    protected $casts = [
        'periodo_desde' => 'date',
        'periodo_hasta' => 'date',
        'fecha_salida' => 'date',
        'fecha_reincorporacion' => 'date',
        'monto_bono_vacacional' => 'decimal:2',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function suplente(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'suplente_id');
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
