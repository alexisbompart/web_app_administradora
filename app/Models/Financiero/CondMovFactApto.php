<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondMovFactApto extends Model
{
    protected $table = 'cond_movs_fact_apto';

    protected $fillable = [
        'compania_id', 'edificio_id', 'apartamento_id', 'periodo',
        'gasto_id', 'concepto', 'monto', 'tipo',
        'administrado', 'agua', 'agua_num', 'alicuota',
        'asoc_vecino', 'asoc_vecino_num', 'cant_chq_dev',
        'chq_dev', 'chq_dev_num', 'cod_edif_legacy', 'cod_edif_ppal',
        'compania_legacy', 'convenios', 'convenios_num', 'demandado',
        'deuda_max', 'deuda_min', 'fdo_especial', 'fdo_especial_num',
        'fecha_fact', 'gestiones', 'gestiones_num', 'honorarios',
        'honorarios_num', 'impuestos', 'impuestos_num', 'int_mora',
        'int_mora_num', 'mes_deuda', 'montol_parcial', 'montol_total',
        'nombre_propietario', 'nro_chq_dev', 'num_apto_legacy',
        'num_consecutivo', 'otros_abonos', 'otros_abonos_num',
        'pago_parcial', 'pago_parcial_num', 'pago_total', 'pago_total_num',
        'porc_gestiones', 'porc_gest_adm', 'porc_int_mora', 'serial',
        'telegramas', 'telegramas_num', 'tipo_pago', 'total_no_comun',
        'total_no_comun_num', 'legacy_created_by', 'legacy_created_at',
        'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2', 'fecha_fact' => 'date',
        'deuda_max' => 'date', 'deuda_min' => 'date',
        'legacy_created_at' => 'datetime', 'legacy_updated_at' => 'datetime',
    ];

    public function compania(): BelongsTo { return $this->belongsTo(Compania::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
    public function apartamento(): BelongsTo { return $this->belongsTo(Apartamento::class); }
}
