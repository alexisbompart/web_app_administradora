<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagointegraltransfdet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transferencia_id')->constrained('pagointegraltransferencia')->cascadeOnDelete();
            $table->foreignId('pagointegral_id')->nullable()->constrained('pagointegral')->nullOnDelete();
            $table->decimal('monto', 15, 2);
            $table->string('concepto', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagointegraltransfdet');
    }
};
