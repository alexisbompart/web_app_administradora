<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_cajeros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cond_cajas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha_asignacion');
            $table->date('fecha_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['caja_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_cajeros');
    }
};
