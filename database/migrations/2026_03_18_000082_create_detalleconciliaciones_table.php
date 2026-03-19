<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalleconciliaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conciliacion_id')->constrained('conciliaciones')->cascadeOnDelete();
            $table->text('descripcion');
            $table->decimal('monto', 15, 2);
            $table->char('tipo', 1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalleconciliaciones');
    }
};
