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
        // Legacy columns
        'administrado',
        'alicuota_especial',
        'avenida',
        'calle',
        'cargar_honorario',
        'celular',
        'ciudad',
        'cod_edif_legacy',
        'cod_pint',
        'cod_ref',
        'contribuye',
        'demandado',
        'emision_recibo',
        'enviar_edo_cta',
        'fax',
        'fecha_cumple',
        'fec_ult_consolidacion',
        'localidad',
        'nro_consolidacion',
        'observacion',
        'pais',
        'rif',
        'telefono_ofic',
        'tipo_doc',
        'tipo_pago',
        'legacy_created_by',
        'legacy_created_at',
        'legacy_updated_by',
        'legacy_updated_at',
    ];

    protected $casts = [
        'alicuota' => 'decimal:4',
        'alicuota_especial' => 'decimal:9',
        'area_mts' => 'decimal:2',
        'estacionamiento' => 'boolean',
        'demandado' => 'boolean',
        'habitaciones' => 'integer',
        'banos' => 'integer',
        'nro_consolidacion' => 'integer',
        'fecha_cumple' => 'date',
        'fec_ult_consolidacion' => 'date',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
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
