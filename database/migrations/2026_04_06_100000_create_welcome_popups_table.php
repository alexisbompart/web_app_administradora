<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welcome_popups', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('contenido');
            $table->string('imagen')->nullable();
            $table->string('boton_texto')->nullable();
            $table->string('boton_url')->nullable();
            $table->string('icono')->default('fas fa-bullhorn');
            $table->string('color')->default('#273272');
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welcome_popups');
    }
};
