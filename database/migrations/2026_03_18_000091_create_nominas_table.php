<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->string('codigo', 20)->unique();
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->string('tipo', 20)->default('quincenal')->comment('quincenal, mensual, especial');
            $table->decimal('total_asignaciones', 15, 2)->default(0);
            $table->decimal('total_deducciones', 15, 2)->default(0);
            $table->decimal('total_neto', 15, 2)->default(0);
            $table->string('estatus', 20)->default('borrador')->comment('borrador, procesada, pagada, anulada');
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_procesamiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};
