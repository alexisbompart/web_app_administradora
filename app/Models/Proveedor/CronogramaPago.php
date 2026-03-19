<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaPago extends Model
{
    protected $table = 'cronograma_pagos';

    protected $fillable = [
        'factura_proveedor_id',
        'fecha_programada',
        'monto_programado',
        'monto_pagado',
        'forma_pago',
        'referencia_pago',
        'fecha_pago',
        'estatus',
        'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_pago' => 'date',
        'monto_programado' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    public function facturaProveedor(): BelongsTo
    {
        return $this->belongsTo(FacturaProveedor::class);
    }
}
