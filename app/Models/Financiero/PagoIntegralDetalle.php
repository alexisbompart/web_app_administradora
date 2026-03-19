<?php

namespace App\Models\Financiero;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoIntegralDetalle extends Model
{
    protected $table = 'pagointegral_detalle';

    protected $fillable = [
        'pagointegral_id',
        'periodo',
        'monto',
        'concepto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function pagoIntegral(): BelongsTo
    {
        return $this->belongsTo(PagoIntegral::class, 'pagointegral_id');
    }
}
