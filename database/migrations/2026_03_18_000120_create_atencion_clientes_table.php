<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atencion_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('cond_edificios')->nullOnDelete();
            $table->foreignId('propietario_id')->nullable()->constrained('propietarios')->nullOnDelete();
            $table->foreignId('ejecutivo_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 50)->comment('consulta, queja, solicitud, emergencia, asesoria_legal, asamblea');
            $table->string('asunto', 200);
            $table->text('descripcion')->nullable();
            $table->string('prioridad', 20)->default('media')->comment('baja, media, alta, urgente');
            $table->string('estatus', 20)->default('abierto')->comment('abierto, en_proceso, resuelto, cerrado');
            $table->date('fecha_apertura');
            $table->date('fecha_cierre')->nullable();
            $table->text('respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atencion_clientes');
    }
};
