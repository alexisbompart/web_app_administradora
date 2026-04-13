<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('nombres_apellidos');
            $table->string('email');
            $table->string('telefono', 30);
            $table->string('asunto');
            $table->text('descripcion')->nullable();
            $table->enum('estatus', ['pendiente', 'en_revision', 'respondida', 'cerrada'])->default('pendiente');
            $table->text('notas_internas')->nullable();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_servicio');
    }
};
