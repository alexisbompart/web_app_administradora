<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propietario_apartamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propietario_id')->constrained('propietarios')->cascadeOnDelete();
            $table->foreignId('apartamento_id')->constrained('cond_aptos')->cascadeOnDelete();
            $table->date('fecha_desde');
            $table->date('fecha_hasta')->nullable();
            $table->boolean('propietario_actual')->default(true);
            $table->timestamps();

            $table->unique(['propietario_id', 'apartamento_id', 'fecha_desde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propietario_apartamento');
    }
};
