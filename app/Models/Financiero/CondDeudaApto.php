<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CondDeudaApto extends Model
{
    protected $table = 'cond_deudas_apto';

    protected $fillable = [
        'compania_id',
        'edificio_id',
        'apartamento_id',
        'periodo',
        'fecha_emision',
        'fecha_vencimiento',
        'monto_original',
        'monto_mora',
        'monto_interes',
        'monto_descuento',
        'monto_pagado',
        'saldo',
        'estatus',
        'observaciones',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_original' => 'decimal:2',
        'monto_mora' => 'decimal:2',
        'monto_interes' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    public function condPagoAptos(): HasMany
    {
        return $this->hasMany(CondPagoApto::class, 'deuda_id');
    }
}
