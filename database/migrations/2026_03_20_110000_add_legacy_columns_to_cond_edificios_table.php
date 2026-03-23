<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_edificios', function (Blueprint $table) {
            // Legacy operational fields
            $table->string('abogado', 100)->nullable();
            $table->string('adm_abonos', 2)->nullable();
            $table->string('adm_cond', 2)->nullable();
            $table->string('adm_frec_consolidacion', 2)->nullable();
            $table->string('adm_gestion', 2)->nullable();
            $table->string('adm_interes', 2)->nullable();
            $table->string('adm_interes_fdo_reserva', 2)->nullable();
            $table->string('adm_max_consol_apto', 2)->nullable();
            $table->string('adm_max_convenios_apto', 2)->nullable();
            $table->string('adm_max_meses_int', 2)->nullable();
            $table->string('adm_monto_telegramas', 2)->nullable();
            $table->string('adm_porc_fdo_prest_soc', 2)->nullable();
            $table->string('adm_porc_fdo_reserva', 2)->nullable();
            $table->string('adm_porc_pronto_pago', 2)->nullable();
            $table->decimal('alicuota_legacy', 12, 5)->nullable();
            $table->string('alicuota_comun', 20)->nullable();
            $table->date('aum_fec')->nullable();
            $table->decimal('aum_mto_hon', 15, 2)->nullable();
            $table->decimal('aum_mto_hon_num', 15, 2)->nullable();
            $table->string('avenida', 200)->nullable();
            $table->string('calle', 200)->nullable();
            $table->integer('cant_apto')->nullable();
            $table->string('cargo_int_mora', 2)->nullable();
            $table->string('cargo_telegramas', 2)->nullable();
            $table->string('cobrador', 100)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('cod_agrup', 20)->nullable();
            $table->string('cod_cobrador', 20)->nullable();
            $table->string('cod_edif_ppal', 20)->nullable();
            $table->string('cod_junta', 20)->nullable();
            $table->string('cod_proveedor', 20)->nullable();
            $table->string('cod_zona', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('conserje', 100)->nullable();
            $table->string('consolida_gestion', 2)->nullable();
            $table->string('constructora', 200)->nullable();
            $table->string('contrato_trabajo', 2)->nullable();
            $table->string('contribuye', 2)->nullable();
            $table->string('edif_ppal', 2)->nullable();
            $table->string('estado_legacy', 100)->nullable();
            $table->string('faov', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->date('fec_aum_honor')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->date('fecha_bomberos')->nullable();
            $table->date('fecha_habit')->nullable();
            $table->date('fecha_notaria')->nullable();
            $table->date('fecha_reg_doc')->nullable();
            $table->date('fec_doc_cond')->nullable();
            $table->date('fec_ingreso')->nullable();
            $table->date('fec_plazo_gracia')->nullable();
            $table->date('fec_registro')->nullable();
            $table->date('fec_ult_consol')->nullable();
            $table->string('folio_notaria', 50)->nullable();
            $table->string('folio_reg', 50)->nullable();
            $table->integer('frec_consolidacion')->nullable();
            $table->string('gastos_nomina', 2)->nullable();
            $table->string('gestiones', 2)->nullable();
            $table->decimal('honorario_adm', 15, 2)->nullable();
            $table->decimal('honorario_adm_num', 15, 2)->nullable();
            $table->decimal('honorario_esp', 15, 2)->nullable();
            $table->decimal('honorario_esp_num', 15, 2)->nullable();
            $table->string('interes_fdo_reserva', 2)->nullable();
            $table->string('listado_propietarios', 2)->nullable();
            $table->string('localidad', 200)->nullable();
            $table->string('logo_legacy', 200)->nullable();
            $table->string('logo_propio', 2)->nullable();
            $table->integer('max_consol_apto')->nullable();
            $table->integer('max_convenios_apto')->nullable();
            $table->integer('max_meses_int')->nullable();
            $table->integer('meses_extjud')->nullable();
            $table->string('mes_pag_sso', 20)->nullable();
            $table->string('mes_rec_sso', 20)->nullable();
            $table->string('mfda_ant', 20)->nullable();
            $table->decimal('monto_aumento_hon', 15, 2)->nullable();
            $table->decimal('monto_aumento_hon_num', 15, 2)->nullable();
            $table->decimal('monto_telegramas', 15, 2)->nullable();
            $table->decimal('monto_telegramas_num', 15, 2)->nullable();
            $table->decimal('monto_vivienda', 15, 2)->nullable();
            $table->decimal('monto_vivienda_num', 15, 2)->nullable();
            $table->string('nil', 30)->nullable();
            $table->string('nombre_fiscal', 200)->nullable();
            $table->string('nombre_notaria', 200)->nullable();
            $table->string('nombre_registro', 200)->nullable();
            $table->string('nro_doc_cond', 50)->nullable();
            $table->string('nro_doc_notariado', 50)->nullable();
            $table->string('nro_doc_reg', 50)->nullable();
            $table->string('nro_permiso_bomberos', 50)->nullable();
            $table->string('nro_permiso_habit', 50)->nullable();
            $table->integer('num_cons_recibo')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('pais', 100)->nullable();
            $table->string('plano_edif', 200)->nullable();
            $table->integer('plazo_gracia')->nullable();
            $table->decimal('porc_fdo_prest_soc', 8, 2)->nullable();
            $table->decimal('porc_fdo_reserva', 8, 2)->nullable();
            $table->decimal('porc_hon_adm', 8, 2)->nullable();
            $table->decimal('porc_int_mora', 8, 2)->nullable();
            $table->decimal('porc_pronto_pago', 8, 2)->nullable();
            $table->decimal('porc_telegramas', 8, 2)->nullable();
            $table->date('primera_fact')->nullable();
            $table->string('relacion_fdo_prest_soc', 2)->nullable();
            $table->string('relacion_fdo_reserva', 2)->nullable();
            $table->string('service', 2)->nullable();
            $table->string('tipo_honorario', 10)->nullable();
            $table->string('tipo_servicio', 10)->nullable();
            $table->string('tiuna', 2)->nullable();
            $table->string('tomo_adm', 50)->nullable();
            $table->string('tomo_notaria', 50)->nullable();
            $table->string('tomo_reg', 50)->nullable();
            $table->date('ult_fact')->nullable();
            $table->string('vivienda', 2)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_edificios', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
