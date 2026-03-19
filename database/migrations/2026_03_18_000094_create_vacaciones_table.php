<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->date('periodo_desde');
            $table->date('periodo_hasta');
            $table->integer('dias_correspondientes');
            $table->integer('dias_disfrutados')->default(0);
            $table->integer('dias_pendientes')->default(0);
            $table->date('fecha_salida')->nullable();
            $table->date('fecha_reincorporacion')->nullable();
            $table->foreignId('suplente_id')->nullable()->constrained('trabajadores')->nullOnDelete();
            $table->decimal('monto_bono_vacacional', 15, 2)->default(0);
            $table->string('estatus', 20)->default('pendiente')->comment('pendiente, aprobada, en_curso, completada, cancelada');
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
