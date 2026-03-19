<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_descuentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('cond_edificios')->nullOnDelete();
            $table->string('descripcion', 200);
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->decimal('monto_fijo', 15, 2)->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_descuentos');
    }
};
