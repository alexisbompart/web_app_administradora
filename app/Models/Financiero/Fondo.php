<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Compania;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fondo extends Model
{
    protected $table = 'fondos';

    protected $fillable = [
        'compania_id',
        'nombre',
        'tipo',
        'saldo_actual',
        'meta',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'saldo_actual' => 'decimal:2',
        'meta' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function movimientosFondo(): HasMany
    {
        return $this->hasMany(MovimientoFondo::class);
    }
}
