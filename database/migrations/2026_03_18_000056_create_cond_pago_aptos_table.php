<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_pago_aptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('cond_pagos')->cascadeOnDelete();
            $table->foreignId('apartamento_id')->constrained('cond_aptos')->cascadeOnDelete();
            $table->foreignId('deuda_id')->nullable()->constrained('cond_deudas_apto')->nullOnDelete();
            $table->string('periodo', 7);
            $table->decimal('monto_aplicado', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_pago_aptos');
    }
};
