<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_exoneracion_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartamento_id')->constrained('cond_aptos')->cascadeOnDelete();
            $table->string('periodo_desde', 7);
            $table->string('periodo_hasta', 7);
            $table->text('motivo');
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_autorizacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_exoneracion_pagos');
    }
};
