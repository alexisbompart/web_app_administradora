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
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->decimal('tasa_bcv_pago', 15, 6)->nullable()->after('monto_recibido');
        });
    }

    public function down(): void
    {
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->dropColumn('tasa_bcv_pago');
        });
    }
};
