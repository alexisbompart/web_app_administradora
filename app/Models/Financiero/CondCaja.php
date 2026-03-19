<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CondCaja extends Model
{
    protected $table = 'cond_cajas';

    protected $fillable = [
        'compania_id',
        'nombre',
        'ubicacion',
        'saldo',
        'activo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function condCajeros(): HasMany
    {
        return $this->hasMany(CondCajero::class, 'caja_id');
    }
}
