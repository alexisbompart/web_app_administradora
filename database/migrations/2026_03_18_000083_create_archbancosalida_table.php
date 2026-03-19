<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archbancosalida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banco_id')->constrained('bancos')->cascadeOnDelete();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->string('nombre_archivo', 200);
            $table->date('fecha_generacion');
            $table->integer('total_registros')->default(0);
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->char('estatus', 1)->default('G')->comment('G=Generado, E=Enviado, P=Procesado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archbancosalida');
    }
};
