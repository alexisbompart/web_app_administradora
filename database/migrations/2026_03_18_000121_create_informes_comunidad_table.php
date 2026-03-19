<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes_comunidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('cond_edificios')->nullOnDelete();
            $table->string('tipo', 50)->comment('relacion_gastos, estado_cuenta, morosos, informe_anual, plan_operativo, circular');
            $table->string('titulo', 200);
            $table->text('contenido')->nullable();
            $table->string('archivo_path', 200)->nullable();
            $table->string('periodo', 7)->nullable();
            $table->date('fecha_generacion');
            $table->boolean('enviado')->default(false);
            $table->timestamp('fecha_envio')->nullable();
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes_comunidad');
    }
};
