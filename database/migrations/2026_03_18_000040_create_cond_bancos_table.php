<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_bancos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('banco_id')->constrained('bancos')->cascadeOnDelete();
            $table->string('numero_cuenta', 30);
            $table->string('tipo_cuenta', 20)->default('corriente');
            $table->string('titular', 200)->nullable();
            $table->decimal('saldo_actual', 15, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_bancos');
    }
};
