<?php

namespace App\Models\Financiero;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoFondo extends Model
{
    protected $table = 'movimientos_fondos';

    protected $fillable = [
        'fondo_id',
        'tipo_movimiento',
        'monto',
        'saldo_anterior',
        'saldo_posterior',
        'descripcion',
        'referencia',
        'fecha_movimiento',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'fecha_movimiento' => 'date',
    ];

    public function fondo(): BelongsTo
    {
        return $this->belongsTo(Fondo::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
