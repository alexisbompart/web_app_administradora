<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_movs_fact_apto', function (Blueprint $table) {
            $table->string('administrado', 10)->nullable();
            $table->decimal('agua', 15, 2)->nullable();
            $table->decimal('agua_num', 15, 2)->nullable();
            $table->decimal('alicuota', 12, 6)->nullable();
            $table->decimal('asoc_vecino', 15, 2)->nullable();
            $table->decimal('asoc_vecino_num', 15, 2)->nullable();
            $table->integer('cant_chq_dev')->nullable();
            $table->decimal('chq_dev', 15, 2)->nullable();
            $table->decimal('chq_dev_num', 15, 2)->nullable();
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('cod_edif_ppal', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->decimal('convenios', 15, 2)->nullable();
            $table->decimal('convenios_num', 15, 2)->nullable();
            $table->string('demandado', 2)->nullable();
            $table->date('deuda_max')->nullable();
            $table->date('deuda_min')->nullable();
            $table->decimal('fdo_especial', 15, 2)->nullable();
            $table->decimal('fdo_especial_num', 15, 2)->nullable();
            $table->date('fecha_fact')->nullable();
            $table->decimal('gestiones', 15, 2)->nullable();
            $table->decimal('gestiones_num', 15, 2)->nullable();
            $table->decimal('honorarios', 15, 2)->nullable();
            $table->decimal('honorarios_num', 15, 2)->nullable();
            $table->decimal('impuestos', 15, 2)->nullable();
            $table->decimal('impuestos_num', 15, 2)->nullable();
            $table->decimal('int_mora', 15, 2)->nullable();
            $table->decimal('int_mora_num', 15, 2)->nullable();
            $table->integer('mes_deuda')->nullable();
            $table->string('montol_parcial', 500)->nullable();
            $table->string('montol_total', 500)->nullable();
            $table->string('nombre_propietario', 200)->nullable();
            $table->string('nro_chq_dev', 50)->nullable();
            $table->string('num_apto_legacy', 20)->nullable();
            $table->integer('num_consecutivo')->nullable();
            $table->decimal('otros_abonos', 15, 2)->nullable();
            $table->decimal('otros_abonos_num', 15, 2)->nullable();
            $table->decimal('pago_parcial', 15, 2)->nullable();
            $table->decimal('pago_parcial_num', 15, 2)->nullable();
            $table->decimal('pago_total', 15, 2)->nullable();
            $table->decimal('pago_total_num', 15, 2)->nullable();
            $table->decimal('porc_gestiones', 8, 2)->nullable();
            $table->decimal('porc_gest_adm', 8, 2)->nullable();
            $table->decimal('porc_int_mora', 8, 2)->nullable();
            $table->string('serial', 50)->nullable();
            $table->decimal('telegramas', 15, 2)->nullable();
            $table->decimal('telegramas_num', 15, 2)->nullable();
            $table->string('tipo_pago', 10)->nullable();
            $table->decimal('total_no_comun', 15, 2)->nullable();
            $table->decimal('total_no_comun_num', 15, 2)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_movs_fact_apto', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
