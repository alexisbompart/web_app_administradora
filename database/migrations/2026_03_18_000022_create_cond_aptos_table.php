<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_aptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edificio_id')->constrained('cond_edificios')->cascadeOnDelete();
            $table->string('num_apto', 10);
            $table->string('piso', 10)->nullable();
            $table->decimal('area_mts', 8, 2)->nullable();
            $table->decimal('alicuota', 8, 4)->default(0);
            $table->integer('habitaciones')->default(0);
            $table->integer('banos')->default(0);
            $table->boolean('estacionamiento')->default(false);
            $table->string('propietario_nombre', 200)->nullable();
            $table->string('propietario_cedula', 20)->nullable();
            $table->string('propietario_telefono', 20)->nullable();
            $table->string('propietario_email', 100)->nullable();
            $table->char('estatus', 1)->default('A');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['edificio_id', 'num_apto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_aptos');
    }
};
