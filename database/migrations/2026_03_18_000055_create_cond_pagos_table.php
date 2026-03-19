<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('edificio_id')->constrained('cond_edificios')->cascadeOnDelete();
            $table->date('fecha_pago');
            $table->string('numero_recibo', 50)->nullable()->unique();
            $table->string('forma_pago', 30)->comment('efectivo, transferencia, deposito, tarjeta, pago_integral');
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->string('numero_referencia', 100)->nullable();
            $table->decimal('monto_total', 15, 2);
            $table->decimal('monto_recibido', 15, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->char('estatus', 1)->default('A')->comment('A=Activo, N=Anulado, P=Pendiente');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_pagos');
    }
};
