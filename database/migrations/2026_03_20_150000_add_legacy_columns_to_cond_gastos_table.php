<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_gastos', function (Blueprint $table) {
            $table->string('aplica_idb', 2)->nullable();
            $table->string('clasificacion', 10)->nullable();
            $table->string('cod_contable2', 50)->nullable();
            $table->string('cod_contable3', 50)->nullable();
            $table->string('cod_gasto', 20)->nullable();
            $table->string('cod_gasto_nomina', 20)->nullable();
            $table->string('cod_grupo', 20)->nullable();
            $table->string('cod_impuesto', 20)->nullable();
            $table->string('cod_maestro_contable', 50)->nullable();
            $table->string('cta_individual', 50)->nullable();
            $table->string('cta_ind_intercompania', 50)->nullable();
            $table->integer('cuotas')->nullable();
            $table->string('diferible', 2)->nullable();
            $table->string('empleados', 2)->nullable();
            $table->string('es_fondo', 2)->nullable();
            $table->string('exento', 2)->nullable();
            $table->string('exonerable', 2)->nullable();
            $table->string('facturable', 2)->nullable();
            $table->string('fondo', 20)->nullable();
            $table->string('fraccionable', 2)->nullable();
            $table->string('gasto_alterno', 20)->nullable();
            $table->string('imagen', 200)->nullable();
            $table->string('imagen_gasto', 200)->nullable();
            $table->string('islr', 2)->nullable();
            $table->string('presupuestable', 2)->nullable();
            $table->string('redondear', 2)->nullable();
            $table->string('tipo_calculo', 10)->nullable();
            $table->string('tipo_gasto', 10)->nullable();
            $table->string('tipo_negocio', 10)->nullable();
            $table->string('transferencia', 2)->nullable();
            $table->string('zona', 20)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_gastos', function (Blueprint $table) {
            $table->dropColumn([
                'aplica_idb', 'clasificacion', 'cod_contable2', 'cod_contable3',
                'cod_gasto', 'cod_gasto_nomina', 'cod_grupo', 'cod_impuesto',
                'cod_maestro_contable', 'cta_individual', 'cta_ind_intercompania',
                'cuotas', 'diferible', 'empleados', 'es_fondo', 'exento',
                'exonerable', 'facturable', 'fondo', 'fraccionable', 'gasto_alterno',
                'imagen', 'imagen_gasto', 'islr', 'presupuestable', 'redondear',
                'tipo_calculo', 'tipo_gasto', 'tipo_negocio', 'transferencia', 'zona',
                'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
