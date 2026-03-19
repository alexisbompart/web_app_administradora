<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos_trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->decimal('monto_prestamo', 15, 2);
            $table->decimal('monto_cuota', 15, 2);
            $table->integer('cuotas_totales');
            $table->integer('cuotas_pagadas')->default(0);
            $table->decimal('saldo_pendiente', 15, 2);
            $table->date('fecha_solicitud');
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_inicio_descuento')->nullable();
            $table->decimal('tasa_interes', 5, 2)->default(0);
            $table->string('estatus', 20)->default('solicitado')->comment('solicitado, aprobado, activo, cancelado, rechazado');
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos_trabajadores');
    }
};
