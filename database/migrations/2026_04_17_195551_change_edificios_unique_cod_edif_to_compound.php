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
        Schema::table('cond_edificios', function (Blueprint $table) {
            // Eliminar el unique solo por cod_edif
            $table->dropUnique('cond_edificios_cod_edif_unique');
            // Crear unique compuesto: mismo cod_edif + compania_id no puede repetirse
            $table->unique(['cod_edif', 'compania_id'], 'cond_edificios_cod_edif_compania_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cond_edificios', function (Blueprint $table) {
            $table->dropUnique('cond_edificios_cod_edif_compania_unique');
            $table->unique('cod_edif', 'cond_edificios_cod_edif_unique');
        });
    }
};
