<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_tasas_bcv', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('moneda', 10);
            $table->decimal('tasa', 15, 6);
            $table->string('fuente', 50)->nullable();
            $table->timestamps();

            $table->unique(['fecha', 'moneda']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_tasas_bcv');
    }
};
