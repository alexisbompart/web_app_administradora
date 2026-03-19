<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagointegraltransferencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->foreignId('banco_origen_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->foreignId('banco_destino_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('monto', 15, 2);
            $table->string('referencia', 100)->nullable();
            $table->char('estatus', 1)->default('P');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagointegraltransferencia');
    }
};
