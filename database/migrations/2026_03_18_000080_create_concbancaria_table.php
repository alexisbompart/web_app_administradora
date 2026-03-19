<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concbancaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cond_banco_id')->constrained('cond_bancos')->cascadeOnDelete();
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->decimal('saldo_banco', 15, 2)->default(0);
            $table->decimal('saldo_libros', 15, 2)->default(0);
            $table->decimal('diferencia', 15, 2)->default(0);
            $table->char('estatus', 1)->default('P')->comment('P=Pendiente, C=Conciliada');
            $table->foreignId('realizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concbancaria');
    }
};
