<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagointegral_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagointegral_id')->constrained('pagointegral')->cascadeOnDelete();
            $table->string('periodo', 7);
            $table->decimal('monto', 15, 2);
            $table->string('concepto', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagointegral_detalle');
    }
};
