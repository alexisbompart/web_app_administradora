<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cond_gastos', function (Blueprint $table) {
            // Unique per (cod_gasto, tipo_gasto) — legacy gastos have same cod_gasto with tipo_gasto 0/1/2/3
            $table->unique(['cod_gasto', 'tipo_gasto'], 'cond_gastos_cod_gasto_tipo_gasto_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cond_gastos', function (Blueprint $table) {
            $table->dropUnique('cond_gastos_cod_gasto_tipo_gasto_unique');
        });
    }
};
