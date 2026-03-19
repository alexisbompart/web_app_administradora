<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestaciones_sociales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->integer('anio');
            $table->integer('trimestre')->nullable();
            $table->integer('dias_acumulados')->default(0);
            $table->decimal('monto_acumulado', 15, 2)->default(0);
            $table->decimal('intereses', 15, 2)->default(0);
            $table->decimal('anticipos', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->date('fecha_calculo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestaciones_sociales');
    }
};
