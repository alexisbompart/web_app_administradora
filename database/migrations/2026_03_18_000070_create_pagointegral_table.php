<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagointegral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afilpagointegral_id')->nullable()->constrained('afilpagointegral')->nullOnDelete();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->date('fecha');
            $table->decimal('monto_total', 15, 2);
            $table->string('forma_pago', 30);
            $table->string('referencia', 100)->nullable();
            $table->char('estatus', 1)->default('P')->comment('P=Pendiente, A=Aprobado, R=Rechazado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagointegral');
    }
};
