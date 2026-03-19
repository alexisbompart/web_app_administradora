<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('cond_edificios')->nullOnDelete();
            $table->string('codigo', 20);
            $table->string('descripcion', 200);
            $table->string('tipo', 50)->nullable()->comment('fijo, variable, extraordinario');
            $table->decimal('monto_base', 15, 2)->default(0);
            $table->boolean('aplica_alicuota')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['compania_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_gastos');
    }
};
