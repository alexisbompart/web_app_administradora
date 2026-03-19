<?php

namespace App\Models\Financiero;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConcBancaria extends Model
{
    protected $table = 'concbancaria';

    protected $fillable = [
        'cond_banco_id',
        'fecha_desde',
        'fecha_hasta',
        'saldo_banco',
        'saldo_libros',
        'diferencia',
        'estatus',
        'realizado_por',
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'saldo_banco' => 'decimal:2',
        'saldo_libros' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function condBanco(): BelongsTo
    {
        return $this->belongsTo(CondBanco::class, 'cond_banco_id');
    }

    public function conciliaciones(): HasMany
    {
        return $this->hasMany(Conciliacion::class);
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
