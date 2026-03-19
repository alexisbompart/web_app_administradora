<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondPagoApto extends Model
{
    protected $table = 'cond_pago_aptos';

    protected $fillable = [
        'pago_id',
        'apartamento_id',
        'deuda_id',
        'periodo',
        'monto_aplicado',
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
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

    public function condDeudaApto(): BelongsTo
    {
        return $this->belongsTo(CondDeudaApto::class, 'deuda_id');
    }
}
