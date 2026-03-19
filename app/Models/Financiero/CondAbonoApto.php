<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondAbonoApto extends Model
{
    protected $table = 'cond_abonos_apto';

    protected $fillable = [
        'compania_id',
        'apartamento_id',
        'fecha',
        'monto',
        'tipo',
        'referencia',
        'observaciones',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
