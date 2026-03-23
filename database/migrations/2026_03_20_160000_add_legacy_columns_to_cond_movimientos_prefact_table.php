<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_movimientos_prefact', function (Blueprint $table) {
            $table->foreignId('apartamento_id')->nullable()->after('edificio_id')->constrained('cond_aptos')->nullOnDelete();
            $table->string('ampl_concepto', 500)->nullable();
            $table->string('aplicar_gasto_adm', 2)->nullable();
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('cod_gasto_legacy', 20)->nullable();
            $table->string('cod_grupo', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('comprobante_contable', 50)->nullable();
            $table->string('cont_difer', 10)->nullable();
            $table->integer('cuota')->nullable();
            $table->text('ext_concepto')->nullable();
            $table->text('ext_descripcion')->nullable();
            $table->date('fecha_contable')->nullable();
            $table->date('fecha_fact')->nullable();
            $table->string('fondo_reserva', 2)->nullable();
            $table->string('id_convenio', 20)->nullable();
            $table->string('id_factura', 20)->nullable();
            $table->string('id_financiamiento', 20)->nullable();
            $table->string('id_fraccion', 20)->nullable();
            $table->string('id_gasto_dep', 20)->nullable();
            $table->string('id_minuta', 20)->nullable();
            $table->string('id_prov_usada', 20)->nullable();
            $table->decimal('monto_num', 15, 2)->nullable();
            $table->bigInteger('mov_id')->nullable();
            $table->string('num_apto_legacy', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->text('observacion_audit')->nullable();
            $table->string('origen', 50)->nullable();
            $table->string('procesado', 2)->nullable();
            $table->string('provision', 2)->nullable();
            $table->string('recuperable', 2)->nullable();
            $table->string('tipo_fact', 10)->nullable();
            $table->string('tipo_gasto_legacy', 10)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_movimientos_prefact', function (Blueprint $table) {
            $table->dropForeign(['apartamento_id']);
            $table->dropColumn([
                'apartamento_id', 'ampl_concepto', 'aplicar_gasto_adm',
                'cod_edif_legacy', 'cod_gasto_legacy', 'cod_grupo', 'compania_legacy',
                'comprobante_contable', 'cont_difer', 'cuota', 'ext_concepto',
                'ext_descripcion', 'fecha_contable', 'fecha_fact', 'fondo_reserva',
                'id_convenio', 'id_factura', 'id_financiamiento', 'id_fraccion',
                'id_gasto_dep', 'id_minuta', 'id_prov_usada', 'monto_num', 'mov_id',
                'num_apto_legacy', 'observaciones', 'observacion_audit', 'origen',
                'procesado', 'provision', 'recuperable', 'tipo_fact',
                'tipo_gasto_legacy', 'legacy_created_by', 'legacy_created_at',
                'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
