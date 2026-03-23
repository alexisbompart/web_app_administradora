<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_deudas_apto', function (Blueprint $table) {
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('num_apto_legacy', 20)->nullable();
            $table->string('serial', 50)->nullable();
            $table->string('serial_gd', 50)->nullable();
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('descuento_num', 15, 2)->nullable();
            $table->decimal('descuento_old', 15, 2)->nullable();
            $table->decimal('descuento_old_num', 15, 2)->nullable();
            $table->date('fecha_pag')->nullable();
            $table->decimal('gestiones', 15, 2)->nullable();
            $table->decimal('gestiones_num', 15, 2)->nullable();
            $table->decimal('gestiones_old', 15, 2)->nullable();
            $table->decimal('gestiones_old_num', 15, 2)->nullable();
            $table->decimal('gest_consolidadas', 15, 2)->nullable();
            $table->decimal('gest_consolidadas_num', 15, 2)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_deudas_apto', function (Blueprint $table) {
            $table->dropColumn([
                'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
                'serial', 'serial_gd', 'descuento', 'descuento_num',
                'descuento_old', 'descuento_old_num', 'fecha_pag',
                'gestiones', 'gestiones_num', 'gestiones_old', 'gestiones_old_num',
                'gest_consolidadas', 'gest_consolidadas_num',
                'legacy_created_by', 'legacy_created_at',
                'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
