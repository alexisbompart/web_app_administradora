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
        'compania_id', 'edificio_id', 'codigo', 'descripcion', 'tipo',
        'monto_base', 'aplica_alicuota', 'activo',
        // Legacy
        'aplica_idb', 'clasificacion', 'cod_contable2', 'cod_contable3',
        'cod_gasto', 'cod_gasto_nomina', 'cod_grupo', 'cod_impuesto',
        'cod_maestro_contable', 'cta_individual', 'cta_ind_intercompania',
        'cuotas', 'diferible', 'empleados', 'es_fondo', 'exento',
        'exonerable', 'facturable', 'fondo', 'fraccionable', 'gasto_alterno',
        'imagen', 'imagen_gasto', 'islr', 'presupuestable', 'redondear',
        'tipo_calculo', 'tipo_gasto', 'tipo_negocio', 'transferencia', 'zona',
        'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'monto_base' => 'decimal:2',
        'aplica_alicuota' => 'boolean',
        'activo' => 'boolean',
        'cuotas' => 'integer',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
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
