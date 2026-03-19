<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welcome_products', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slogan')->nullable();
            $table->text('descripcion');
            $table->string('icono')->default('fas fa-building');
            $table->string('color')->default('#7f1d1d');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welcome_products');
    }
};
