<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afilapto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartamento_id')->constrained('cond_aptos')->cascadeOnDelete();
            $table->foreignId('edificio_id')->constrained('cond_edificios')->cascadeOnDelete();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->char('estatus_afil', 1)->default('A');
            $table->date('fecha_afiliacion');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('estatus_afil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afilapto');
    }
};
