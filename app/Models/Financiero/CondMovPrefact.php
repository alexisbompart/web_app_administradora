<?php

namespace App\Models\Financiero;

use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CondMovPrefact extends Model
{
    protected $table = 'cond_movimientos_prefact';

    protected $fillable = [
        'compania_id', 'edificio_id', 'apartamento_id', 'periodo',
        'gasto_id', 'concepto', 'monto', 'tipo', 'estatus',
        'ampl_concepto', 'aplicar_gasto_adm', 'cod_edif_legacy',
        'cod_gasto_legacy', 'cod_grupo', 'compania_legacy',
        'comprobante_contable', 'cont_difer', 'cuota', 'ext_concepto',
        'ext_descripcion', 'fecha_contable', 'fecha_fact', 'fondo_reserva',
        'id_convenio', 'id_factura', 'id_financiamiento', 'id_fraccion',
        'id_gasto_dep', 'id_minuta', 'id_prov_usada', 'monto_num', 'mov_id',
        'num_apto_legacy', 'observaciones', 'observacion_audit', 'origen',
        'procesado', 'provision', 'recuperable', 'tipo_fact',
        'tipo_gasto_legacy', 'legacy_created_by', 'legacy_created_at',
        'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'monto_num' => 'decimal:2',
        'cuota' => 'integer',
        'mov_id' => 'integer',
        'fecha_contable' => 'date',
        'fecha_fact' => 'date',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
    ];

    public function compania(): BelongsTo { return $this->belongsTo(Compania::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
    public function apartamento(): BelongsTo { return $this->belongsTo(Apartamento::class); }
    public function gasto(): BelongsTo { return $this->belongsTo(CondGasto::class, 'gasto_id'); }
}
