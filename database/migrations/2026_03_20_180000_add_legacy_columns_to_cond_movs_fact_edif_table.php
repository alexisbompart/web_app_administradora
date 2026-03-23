<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_movs_fact_edif', function (Blueprint $table) {
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            // Fondos - Abonos
            $table->decimal('abonos_fdo_agua', 15, 2)->nullable();
            $table->decimal('abonos_fdo_agua_num', 15, 2)->nullable();
            $table->decimal('abonos_fdo_cont', 15, 2)->nullable();
            $table->decimal('abonos_fdo_cont_num', 15, 2)->nullable();
            $table->decimal('abonos_fdo_esp', 15, 2)->nullable();
            $table->decimal('abonos_fdo_esp_num', 15, 2)->nullable();
            $table->decimal('abonos_fdo_res', 15, 2)->nullable();
            $table->decimal('abonos_fdo_res_num', 15, 2)->nullable();
            $table->decimal('abonos_fdo_soc', 15, 2)->nullable();
            $table->decimal('abonos_fdo_soc_num', 15, 2)->nullable();
            // Fondos - Cargos
            $table->decimal('cargos_fdo_agua', 15, 2)->nullable();
            $table->decimal('cargos_fdo_agua_num', 15, 2)->nullable();
            $table->decimal('cargos_fdo_cont', 15, 2)->nullable();
            $table->decimal('cargos_fdo_cont_num', 15, 2)->nullable();
            $table->decimal('cargos_fdo_esp', 15, 2)->nullable();
            $table->decimal('cargos_fdo_esp_num', 15, 2)->nullable();
            $table->decimal('cargos_fdo_res', 15, 2)->nullable();
            $table->decimal('cargos_fdo_res_num', 15, 2)->nullable();
            $table->decimal('cargos_fdo_soc', 15, 2)->nullable();
            $table->decimal('cargos_fdo_soc_num', 15, 2)->nullable();
            // Cobranza/Deuda/Facturacion
            $table->decimal('cobranza_edif', 15, 2)->nullable();
            $table->decimal('cobranza_edif_num', 15, 2)->nullable();
            $table->decimal('deuda_act_edif', 15, 2)->nullable();
            $table->decimal('deuda_act_edif_num', 15, 2)->nullable();
            $table->decimal('deuda_ant_edif', 15, 2)->nullable();
            $table->decimal('deuda_ant_edif_num', 15, 2)->nullable();
            $table->decimal('facturacion_edif', 15, 2)->nullable();
            $table->decimal('facturacion_edif_num', 15, 2)->nullable();
            // Fechas
            $table->date('fecha_calculo')->nullable();
            $table->date('fecha_fact')->nullable();
            // Intereses y porcentajes
            $table->decimal('int_fdo_res', 15, 2)->nullable();
            $table->decimal('int_fdo_res_num', 15, 2)->nullable();
            $table->decimal('monto_porc_dev_int', 15, 2)->nullable();
            $table->decimal('monto_porc_dev_int_num', 15, 2)->nullable();
            $table->integer('plazo_gracia')->nullable();
            $table->decimal('porc_dev_int', 8, 2)->nullable();
            $table->decimal('porc_fdo_res', 8, 2)->nullable();
            $table->integer('recibos_pend')->nullable();
            $table->string('redondeo', 2)->nullable();
            // Saldos actuales fondos
            $table->decimal('sdo_act_fdo_agua', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_agua_num', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_cont', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_cont_num', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_esp', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_esp_num', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_res', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_res_num', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_soc', 15, 2)->nullable();
            $table->decimal('sdo_act_fdo_soc_num', 15, 2)->nullable();
            // Saldos anteriores fondos
            $table->decimal('sdo_ant_fdo_agua', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_agua_num', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_cont', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_cont_num', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_esp', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_esp_num', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_res', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_res_num', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_soc', 15, 2)->nullable();
            $table->decimal('sdo_ant_fdo_soc_num', 15, 2)->nullable();
            // Legacy audit
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_movs_fact_edif', function (Blueprint $table) {
            $table->dropColumn([
                'cod_edif_legacy', 'compania_legacy',
                'abonos_fdo_agua', 'abonos_fdo_agua_num', 'abonos_fdo_cont', 'abonos_fdo_cont_num',
                'abonos_fdo_esp', 'abonos_fdo_esp_num', 'abonos_fdo_res', 'abonos_fdo_res_num',
                'abonos_fdo_soc', 'abonos_fdo_soc_num', 'cargos_fdo_agua', 'cargos_fdo_agua_num',
                'cargos_fdo_cont', 'cargos_fdo_cont_num', 'cargos_fdo_esp', 'cargos_fdo_esp_num',
                'cargos_fdo_res', 'cargos_fdo_res_num', 'cargos_fdo_soc', 'cargos_fdo_soc_num',
                'cobranza_edif', 'cobranza_edif_num', 'deuda_act_edif', 'deuda_act_edif_num',
                'deuda_ant_edif', 'deuda_ant_edif_num', 'facturacion_edif', 'facturacion_edif_num',
                'fecha_calculo', 'fecha_fact', 'int_fdo_res', 'int_fdo_res_num',
                'monto_porc_dev_int', 'monto_porc_dev_int_num', 'plazo_gracia',
                'porc_dev_int', 'porc_fdo_res', 'recibos_pend', 'redondeo',
                'sdo_act_fdo_agua', 'sdo_act_fdo_agua_num', 'sdo_act_fdo_cont', 'sdo_act_fdo_cont_num',
                'sdo_act_fdo_esp', 'sdo_act_fdo_esp_num', 'sdo_act_fdo_res', 'sdo_act_fdo_res_num',
                'sdo_act_fdo_soc', 'sdo_act_fdo_soc_num', 'sdo_ant_fdo_agua', 'sdo_ant_fdo_agua_num',
                'sdo_ant_fdo_cont', 'sdo_ant_fdo_cont_num', 'sdo_ant_fdo_esp', 'sdo_ant_fdo_esp_num',
                'sdo_ant_fdo_res', 'sdo_ant_fdo_res_num', 'sdo_ant_fdo_soc', 'sdo_ant_fdo_soc_num',
                'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
