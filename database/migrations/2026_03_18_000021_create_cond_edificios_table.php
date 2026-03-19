<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_edificios', function (Blueprint $table) {
            $table->id();
            $table->string('cod_edif', 10)->unique();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->string('nombre', 200);
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('total_aptos')->default(0);
            $table->string('rif', 20)->nullable();
            $table->string('nit', 20)->nullable();
            $table->decimal('alicuota_base', 8, 4)->default(0);
            $table->decimal('fondo_reserva_porcentaje', 5, 2)->default(5);
            $table->integer('dia_corte')->default(1);
            $table->integer('dia_vencimiento')->default(15);
            $table->decimal('mora_porcentaje', 5, 2)->default(0);
            $table->decimal('interes_mora_porcentaje', 5, 2)->default(0);
            $table->string('cuenta_bancaria', 30)->nullable();
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_edificios');
    }
};
