<?php

namespace App\Models\Personal;

use App\Models\Condominio\Compania;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nomina extends Model
{
    use SoftDeletes;

    protected $table = 'nominas';

    protected $fillable = [
        'compania_id',
        'codigo',
        'periodo_inicio',
        'periodo_fin',
        'tipo',
        'total_asignaciones',
        'total_deducciones',
        'total_neto',
        'estatus',
        'procesado_por',
        'fecha_procesamiento',
        'observaciones',
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'total_asignaciones' => 'decimal:2',
        'total_deducciones' => 'decimal:2',
        'total_neto' => 'decimal:2',
        'fecha_procesamiento' => 'datetime',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function procesadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    public function nominaDetalles(): HasMany
    {
        return $this->hasMany(NominaDetalle::class);
    }
}
