<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fondos_beneficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->string('tipo', 50)->comment('LPH, SSO, fondo_social');
            $table->date('fecha');
            $table->char('tipo_movimiento', 1)->comment('I=Ingreso, E=Egreso');
            $table->decimal('monto', 15, 2);
            $table->string('referencia', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fondos_beneficios');
    }
};
