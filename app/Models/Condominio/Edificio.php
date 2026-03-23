<?php

namespace App\Models\Condominio;

use App\Models\Catalogo\Estado;
use App\Models\Financiero\Banco;
use App\Models\Financiero\CondGasto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edificio extends Model
{
    use SoftDeletes;

    protected $table = 'cond_edificios';

    protected $fillable = [
        'cod_edif', 'compania_id', 'nombre', 'direccion', 'ciudad',
        'estado_id', 'telefono', 'email', 'total_aptos', 'rif', 'nit',
        'alicuota_base', 'fondo_reserva_porcentaje', 'dia_corte',
        'dia_vencimiento', 'mora_porcentaje', 'interes_mora_porcentaje',
        'cuenta_bancaria', 'banco_id', 'activo', 'latitud', 'longitud',
        // Legacy columns
        'abogado', 'adm_abonos', 'adm_cond', 'adm_frec_consolidacion',
        'adm_gestion', 'adm_interes', 'adm_interes_fdo_reserva',
        'adm_max_consol_apto', 'adm_max_convenios_apto', 'adm_max_meses_int',
        'adm_monto_telegramas', 'adm_porc_fdo_prest_soc', 'adm_porc_fdo_reserva',
        'adm_porc_pronto_pago', 'alicuota_legacy', 'alicuota_comun',
        'aum_fec', 'aum_mto_hon', 'aum_mto_hon_num', 'avenida', 'calle',
        'cant_apto', 'cargo_int_mora', 'cargo_telegramas', 'cobrador',
        'codigo_postal', 'cod_agrup', 'cod_cobrador', 'cod_edif_ppal',
        'cod_junta', 'cod_proveedor', 'cod_zona', 'compania_legacy',
        'conserje', 'consolida_gestion', 'constructora', 'contrato_trabajo',
        'contribuye', 'edif_ppal', 'estado_legacy', 'faov', 'fax',
        'fec_aum_honor', 'fecha_baja', 'fecha_bomberos', 'fecha_habit',
        'fecha_notaria', 'fecha_reg_doc', 'fec_doc_cond', 'fec_ingreso',
        'fec_plazo_gracia', 'fec_registro', 'fec_ult_consol',
        'folio_notaria', 'folio_reg', 'frec_consolidacion', 'gastos_nomina',
        'gestiones', 'honorario_adm', 'honorario_adm_num', 'honorario_esp',
        'honorario_esp_num', 'interes_fdo_reserva', 'listado_propietarios',
        'localidad', 'logo_legacy', 'logo_propio', 'max_consol_apto',
        'max_convenios_apto', 'max_meses_int', 'meses_extjud', 'mes_pag_sso',
        'mes_rec_sso', 'mfda_ant', 'monto_aumento_hon', 'monto_aumento_hon_num',
        'monto_telegramas', 'monto_telegramas_num', 'monto_vivienda',
        'monto_vivienda_num', 'nil', 'nombre_fiscal', 'nombre_notaria',
        'nombre_registro', 'nro_doc_cond', 'nro_doc_notariado', 'nro_doc_reg',
        'nro_permiso_bomberos', 'nro_permiso_habit', 'num_cons_recibo',
        'observaciones', 'pais', 'plano_edif', 'plazo_gracia',
        'porc_fdo_prest_soc', 'porc_fdo_reserva', 'porc_hon_adm',
        'porc_int_mora', 'porc_pronto_pago', 'porc_telegramas', 'primera_fact',
        'relacion_fdo_prest_soc', 'relacion_fdo_reserva', 'service',
        'tipo_honorario', 'tipo_servicio', 'tiuna', 'tomo_adm', 'tomo_notaria',
        'tomo_reg', 'ult_fact', 'vivienda', 'legacy_created_by',
        'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'total_aptos' => 'integer',
        'alicuota_base' => 'decimal:4',
        'alicuota_legacy' => 'decimal:5',
        'fondo_reserva_porcentaje' => 'decimal:2',
        'mora_porcentaje' => 'decimal:2',
        'interes_mora_porcentaje' => 'decimal:2',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'cant_apto' => 'integer',
        'frec_consolidacion' => 'integer',
        'max_consol_apto' => 'integer',
        'max_convenios_apto' => 'integer',
        'max_meses_int' => 'integer',
        'meses_extjud' => 'integer',
        'num_cons_recibo' => 'integer',
        'plazo_gracia' => 'integer',
        'aum_fec' => 'date',
        'fec_aum_honor' => 'date',
        'fecha_baja' => 'date',
        'fecha_bomberos' => 'date',
        'fecha_habit' => 'date',
        'fecha_notaria' => 'date',
        'fecha_reg_doc' => 'date',
        'fec_doc_cond' => 'date',
        'fec_ingreso' => 'date',
        'fec_plazo_gracia' => 'date',
        'fec_registro' => 'date',
        'fec_ult_consol' => 'date',
        'primera_fact' => 'date',
        'ult_fact' => 'date',
        'legacy_created_at' => 'datetime',
        'legacy_updated_at' => 'datetime',
    ];

    public function compania(): BelongsTo
    {
        return $this->belongsTo(Compania::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function apartamentos(): HasMany
    {
        return $this->hasMany(Apartamento::class);
    }

    public function condGastos(): HasMany
    {
        return $this->hasMany(CondGasto::class, 'edificio_id');
    }
}
