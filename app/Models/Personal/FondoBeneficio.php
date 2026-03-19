<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FondoBeneficio extends Model
{
    protected $table = 'fondos_beneficios';

    protected $fillable = [
        'trabajador_id',
        'tipo',
        'fecha',
        'tipo_movimiento',
        'monto',
        'referencia',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
