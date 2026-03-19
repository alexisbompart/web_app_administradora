<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_fondos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fondo_id')->constrained('fondos')->cascadeOnDelete();
            $table->char('tipo_movimiento', 1)->comment('I=Ingreso, E=Egreso, T=Transferencia');
            $table->decimal('monto', 15, 2);
            $table->decimal('saldo_anterior', 15, 2);
            $table->decimal('saldo_posterior', 15, 2);
            $table->string('descripcion', 200);
            $table->string('referencia', 100)->nullable();
            $table->date('fecha_movimiento');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_fondos');
    }
};
