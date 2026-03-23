<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make pago_id nullable for imports
        Schema::table('cond_pago_aptos', function (Blueprint $table) {
            $table->foreignId('pago_id')->nullable()->change();
        });

        Schema::table('cond_pago_aptos', function (Blueprint $table) {
            $table->foreignId('compania_id')->nullable()->after('id')->constrained('cond_companias')->nullOnDelete();
            $table->foreignId('edificio_id')->nullable()->after('compania_id')->constrained('cond_edificios')->nullOnDelete();
            $table->decimal('abono_historico', 15, 2)->nullable();
            $table->decimal('abono_historico_num', 15, 2)->nullable();
            $table->string('cajero', 50)->nullable();
            $table->decimal('exoneracion', 15, 2)->nullable();
            $table->decimal('exoneracion_num', 15, 2)->nullable();
            $table->date('fecha_pag')->nullable();
            $table->date('fec_apertura')->nullable();
            $table->string('id_pago_legacy', 50)->nullable();
            $table->string('id_pago_apto_legacy', 50)->nullable();
            $table->integer('meses_a_cancelar')->nullable();
            $table->decimal('monto_pago', 15, 2)->nullable();
            $table->decimal('monto_pago_num', 15, 2)->nullable();
            $table->string('nro_caja', 20)->nullable();
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('num_apto_legacy', 20)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_pago_aptos', function (Blueprint $table) {
            $table->dropForeign(['compania_id']);
            $table->dropForeign(['edificio_id']);
            $table->dropColumn([
                'compania_id', 'edificio_id', 'abono_historico', 'abono_historico_num',
                'cajero', 'exoneracion', 'exoneracion_num', 'fecha_pag', 'fec_apertura',
                'id_pago_legacy', 'id_pago_apto_legacy', 'meses_a_cancelar',
                'monto_pago', 'monto_pago_num', 'nro_caja',
                'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
                'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
