<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_detalle_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('cond_pagos')->cascadeOnDelete();
            $table->string('concepto', 200);
            $table->decimal('monto', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_detalle_pagos');
    }
};
