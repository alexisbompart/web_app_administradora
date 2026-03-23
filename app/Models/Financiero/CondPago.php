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
        'compania_id', 'edificio_id', 'fecha_pago', 'numero_recibo',
        'forma_pago', 'banco_id', 'numero_referencia', 'monto_total',
        'monto_recibido', 'observaciones', 'estatus', 'registrado_por',
        // Legacy
        'cajero', 'cod_motivo', 'compania_legacy', 'comprobante_contable',
        'fecha_contable', 'fecha_apertura', 'id_pago_legacy', 'monto_num',
        'monto_letra', 'nro_caja', 'sub_t_efectivo', 'sub_t_efectivo_num',
        'tipo_pago', 't_abono', 't_abono_num', 't_cheque', 't_cheque_num',
        't_correcpago', 't_correcpago_num', 't_deposito', 't_deposito_num',
        't_dochistoric', 't_dochistoric_num', 't_efectivo', 't_efectivo_num',
        't_tarjeta_credito', 't_tarjeta_credito_num', 't_tarjeta_debito',
        't_tarjeta_debito_num', 't_transferencia', 't_transferencia_num',
        'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'fecha_pago' => 'date', 'fecha_contable' => 'date', 'fecha_apertura' => 'date',
        'monto_total' => 'decimal:2', 'monto_recibido' => 'decimal:2',
        'legacy_created_at' => 'datetime', 'legacy_updated_at' => 'datetime',
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
