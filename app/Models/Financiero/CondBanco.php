<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CondBanco extends Model
{
    protected $table = 'cond_bancos';

    protected $fillable = [
        'compania_id',
        'banco_id',
        'numero_cuenta',
        'tipo_cuenta',
        'titular',
        'saldo_actual',
        'activo',
    ];

    protected $casts = [
        'saldo_actual' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function concBancarias(): HasMany
    {
        return $this->hasMany(ConcBancaria::class, 'cond_banco_id');
    }
}
