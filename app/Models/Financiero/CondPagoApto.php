<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondPagoApto extends Model
{
    protected $table = 'cond_pago_aptos';

    protected $fillable = [
        'pago_id', 'compania_id', 'edificio_id', 'apartamento_id',
        'deuda_id', 'periodo', 'monto_aplicado',
        'abono_historico', 'abono_historico_num', 'cajero',
        'exoneracion', 'exoneracion_num', 'fecha_pag', 'fec_apertura',
        'id_pago_legacy', 'id_pago_apto_legacy', 'meses_a_cancelar',
        'monto_pago', 'monto_pago_num', 'nro_caja',
        'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
        'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
        'monto_pago' => 'decimal:2',
        'fecha_pag' => 'date',
        'fec_apertura' => 'date',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
    ];

    public function condPago(): BelongsTo
    {
        return $this->belongsTo(CondPago::class, 'pago_id');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(CondPago::class, 'pago_id');
    }

    public function deuda(): BelongsTo
    {
        return $this->belongsTo(CondDeudaApto::class, 'deuda_id');
    }

    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Condominio\Edificio::class);
    }

    public function condDeudaApto(): BelongsTo
    {
        return $this->belongsTo(CondDeudaApto::class, 'deuda_id');
    }
}
