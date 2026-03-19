<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Afilpagointegral;
use App\Models\Condominio\Compania;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PagoIntegral extends Model
{
    protected $table = 'pagointegral';

    protected $fillable = [
        'afilpagointegral_id',
        'compania_id',
        'fecha',
        'monto_total',
        'forma_pago',
        'referencia',
        'estatus',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_total' => 'decimal:2',
    ];

    public function afilpagointegral(): BelongsTo
    {
        return $this->belongsTo(Afilpagointegral::class);
    }

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function pagoIntegralDetalles(): HasMany
    {
        return $this->hasMany(PagoIntegralDetalle::class, 'pagointegral_id');
    }
}
