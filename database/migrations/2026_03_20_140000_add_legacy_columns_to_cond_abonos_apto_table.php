<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_abonos_apto', function (Blueprint $table) {
            $table->foreignId('edificio_id')->nullable()->after('compania_id')->constrained('cond_edificios')->nullOnDelete();
            $table->string('periodo', 7)->nullable()->after('fecha')->comment('YYYY-MM');
            $table->decimal('monto_abono_num', 15, 2)->nullable()->after('monto');
            $table->string('tipo_abono', 10)->nullable()->after('tipo');
            $table->string('serial', 50)->nullable()->after('referencia');
            $table->date('fecha_cance')->nullable()->after('serial');
            $table->string('operacion', 10)->nullable()->after('fecha_cance');
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
        Schema::table('cond_abonos_apto', function (Blueprint $table) {
            $table->dropForeign(['edificio_id']);
            $table->dropColumn([
                'edificio_id', 'periodo', 'monto_abono_num', 'tipo_abono',
                'serial', 'fecha_cance', 'operacion',
                'cod_edif_legacy', 'compania_legacy', 'num_apto_legacy',
                'legacy_created_by', 'legacy_created_at',
                'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
