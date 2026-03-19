<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_deudas_apto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('edificio_id')->constrained('cond_edificios')->cascadeOnDelete();
            $table->foreignId('apartamento_id')->constrained('cond_aptos')->cascadeOnDelete();
            $table->string('periodo', 7)->comment('YYYY-MM');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('monto_original', 15, 2);
            $table->decimal('monto_mora', 15, 2)->default(0);
            $table->decimal('monto_interes', 15, 2)->default(0);
            $table->decimal('monto_descuento', 15, 2)->default(0);
            $table->decimal('monto_pagado', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2);
            $table->char('estatus', 1)->default('P')->comment('P=Pendiente, C=Cancelada, X=Anulada');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['compania_id', 'edificio_id', 'apartamento_id', 'periodo'], 'cond_deudas_apto_comp_edif_apto_periodo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_deudas_apto');
    }
};
