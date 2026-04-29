<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar afilpagointegral_id a afilapto (nullable por ahora)
        Schema::table('afilapto', function (Blueprint $table) {
            $table->foreignId('afilpagointegral_id')
                ->nullable()
                ->after('id')
                ->constrained('afilpagointegral')
                ->nullOnDelete();
        });

        // 2. Migrar datos: para cada afilpagointegral existente, apuntar su afilapto
        //    La relación actual es afilpagointegral.afilapto_id → afilapto.id
        //    La nueva es afilapto.afilpagointegral_id → afilpagointegral.id
        DB::statement("
            UPDATE afilapto a
            SET afilpagointegral_id = p.id
            FROM afilpagointegral p
            WHERE p.afilapto_id = a.id
        ");

        // 3. Quitar la FK antigua y la columna afilapto_id de afilpagointegral
        Schema::table('afilpagointegral', function (Blueprint $table) {
            $table->dropForeign(['afilapto_id']);
            $table->dropColumn('afilapto_id');
        });
    }

    public function down(): void
    {
        // Restaurar afilapto_id en afilpagointegral
        Schema::table('afilpagointegral', function (Blueprint $table) {
            $table->foreignId('afilapto_id')
                ->nullable()
                ->after('id')
                ->constrained('afilapto')
                ->cascadeOnDelete();
        });

        // Revertir datos
        DB::statement("
            UPDATE afilpagointegral p
            SET afilapto_id = a.id
            FROM afilapto a
            WHERE a.afilpagointegral_id = p.id
        ");

        // Quitar la columna nueva de afilapto
        Schema::table('afilapto', function (Blueprint $table) {
            $table->dropForeign(['afilpagointegral_id']);
            $table->dropColumn('afilpagointegral_id');
        });
    }
};
