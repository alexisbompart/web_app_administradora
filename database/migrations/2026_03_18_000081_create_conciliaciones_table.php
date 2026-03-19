<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conciliaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concbancaria_id')->constrained('concbancaria')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('referencia', 100)->nullable();
            $table->string('concepto', 200);
            $table->decimal('monto', 15, 2);
            $table->char('tipo', 1)->comment('D=Debito, C=Credito');
            $table->string('origen', 20)->comment('banco, libros');
            $table->boolean('conciliado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conciliaciones');
    }
};
