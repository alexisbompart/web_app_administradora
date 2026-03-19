<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondGasto extends Model
{
    protected $table = 'cond_gastos';

    protected $fillable = [
        'compania_id',
        'edificio_id',
        'codigo',
        'descripcion',
        'tipo',
        'monto_base',
        'aplica_alicuota',
        'activo',
    ];

    protected $casts = [
        'monto_base' => 'decimal:2',
        'aplica_alicuota' => 'boolean',
        'activo' => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }
}
