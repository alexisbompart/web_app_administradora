<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomina_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->integer('dias_trabajados')->default(15);
            $table->decimal('salario_base', 15, 2);
            $table->decimal('horas_extras', 15, 2)->default(0);
            $table->decimal('bono_alimentacion', 15, 2)->default(0);
            $table->decimal('bono_transporte', 15, 2)->default(0);
            $table->decimal('otros_ingresos', 15, 2)->default(0);
            $table->decimal('sso_empleado', 15, 2)->default(0);
            $table->decimal('sso_patronal', 15, 2)->default(0);
            $table->decimal('lph_empleado', 15, 2)->default(0);
            $table->decimal('lph_patronal', 15, 2)->default(0);
            $table->decimal('islr', 15, 2)->default(0);
            $table->decimal('otros_descuentos', 15, 2)->default(0);
            $table->decimal('total_asignaciones', 15, 2)->default(0);
            $table->decimal('total_deducciones', 15, 2)->default(0);
            $table->decimal('neto_pagar', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['nomina_id', 'trabajador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomina_detalles');
    }
};
