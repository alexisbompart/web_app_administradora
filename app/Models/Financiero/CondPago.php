<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondPago extends Model
{
    use SoftDeletes;

    protected $table = 'cond_pagos';

    protected $fillable = [
        'compania_id',
        'edificio_id',
        'fecha_pago',
        'numero_recibo',
        'forma_pago',
        'banco_id',
        'numero_referencia',
        'monto_total',
        'monto_recibido',
        'observaciones',
        'estatus',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto_total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function condPagoAptos(): HasMany
    {
        return $this->hasMany(CondPagoApto::class, 'pago_id');
    }

    public function condDetallePagos(): HasMany
    {
        return $this->hasMany(CondDetallePago::class, 'pago_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
