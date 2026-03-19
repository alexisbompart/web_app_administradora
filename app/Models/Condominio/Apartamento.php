<?php

namespace App\Models\Condominio;

use App\Models\Financiero\CondDeudaApto;
use App\Models\Financiero\CondPagoApto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apartamento extends Model
{
    use SoftDeletes;

    protected $table = 'cond_aptos';

    protected $fillable = [
        'edificio_id',
        'num_apto',
        'piso',
        'area_mts',
        'alicuota',
        'habitaciones',
        'banos',
        'estacionamiento',
        'propietario_nombre',
        'propietario_cedula',
        'propietario_telefono',
        'propietario_email',
        'estatus',
    ];

    protected $casts = [
        'alicuota' => 'decimal:4',
        'area_mts' => 'decimal:2',
        'estacionamiento' => 'boolean',
        'habitaciones' => 'integer',
        'banos' => 'integer',
    ];

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function propietarios(): BelongsToMany
    {
        return $this->belongsToMany(Propietario::class, 'propietario_apartamento')
            ->withTimestamps()
            ->withPivot('fecha_desde', 'fecha_hasta', 'propietario_actual');
    }

    public function condDeudasApto(): HasMany
    {
        return $this->hasMany(CondDeudaApto::class);
    }

    public function condPagosApto(): HasMany
    {
        return $this->hasMany(CondPagoApto::class);
    }
}
