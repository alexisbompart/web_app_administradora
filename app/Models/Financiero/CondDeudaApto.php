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
        'compania_id', 'edificio_id', 'apartamento_id',
        'periodo', 'fecha_emision', 'fecha_vencimiento',
        'monto_original', 'monto_mora', 'monto_interes',
        'monto_descuento', 'monto_pagado', 'saldo',
        'estatus', 'observaciones',
        // Legacy columns
        'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
        'serial', 'serial_gd', 'descuento', 'descuento_num',
        'descuento_old', 'descuento_old_num', 'fecha_pag',
        'gestiones', 'gestiones_num', 'gestiones_old', 'gestiones_old_num',
        'gest_consolidadas', 'gest_consolidadas_num',
        'legacy_created_by', 'legacy_created_at',
        'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_pag' => 'date',
        'monto_original' => 'decimal:2',
        'monto_mora' => 'decimal:2',
        'monto_interes' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
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

    /**
     * Scope: deudas realmente pendientes.
     * Condicion: serial es N o null, Y fecha_pag es null o 0001-01-01.
     */
    public function scopePendientes($query)
    {
        return $query->where(function ($q) {
                $q->whereNull('serial')->orWhere('serial', 'N');
            })
            ->where(function ($q) {
                $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
            });
    }
}
