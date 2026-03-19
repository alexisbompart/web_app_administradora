<?php

namespace App\Models\Financiero;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondDetallePago extends Model
{
    protected $table = 'cond_detalle_pagos';

    protected $fillable = [
        'pago_id',
        'concepto',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function condPago(): BelongsTo
    {
        return $this->belongsTo(CondPago::class, 'pago_id');
    }
}
