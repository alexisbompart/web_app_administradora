<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cronograma_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_proveedor_id')->constrained('facturas_proveedores')->cascadeOnDelete();
            $table->date('fecha_programada');
            $table->decimal('monto_programado', 15, 2);
            $table->decimal('monto_pagado', 15, 2)->default(0);
            $table->string('forma_pago', 30)->nullable();
            $table->string('referencia_pago', 100)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->string('estatus', 20)->default('pendiente')->comment('pendiente, pagado, cancelado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma_pagos');
    }
};
