<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welcome_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('tipo')->default('text'); // text, textarea, image
            $table->string('seccion')->default('general');
            $table->string('etiqueta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welcome_settings');
    }
};
