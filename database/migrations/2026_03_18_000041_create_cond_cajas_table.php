<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->string('ubicacion', 200)->nullable();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_cajas');
    }
};
