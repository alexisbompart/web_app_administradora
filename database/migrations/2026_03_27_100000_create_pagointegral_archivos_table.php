<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagointegral_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banco_id')->constrained('bancos')->cascadeOnDelete();
            $table->string('nombre_archivo', 100);
            $table->string('tipo_archivo', 30)->default('PAGOS_ENVIOS');
            $table->integer('cantidad_pagos')->default(0);
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->char('estatus', 2)->default('GE')->comment('GE=Generado, EN=Enviado, EP=En Proceso, PR=Procesado');
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_generado')->useCurrent();
            $table->timestamp('fecha_enviado')->nullable();
            $table->timestamp('fecha_procesado')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('pagointegral_archivo_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archivo_id')->constrained('pagointegral_archivos')->cascadeOnDelete();
            $table->foreignId('pagointegral_id')->constrained('pagointegral')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagointegral_archivo_pagos');
        Schema::dropIfExists('pagointegral_archivos');
    }
};
