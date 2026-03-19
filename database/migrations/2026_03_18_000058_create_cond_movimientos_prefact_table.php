<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_movimientos_prefact', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->foreignId('edificio_id')->constrained('cond_edificios')->cascadeOnDelete();
            $table->string('periodo', 7);
            $table->foreignId('gasto_id')->nullable()->constrained('cond_gastos')->nullOnDelete();
            $table->string('concepto', 200);
            $table->decimal('monto', 15, 2);
            $table->char('tipo', 1)->comment('D=Debito, C=Credito');
            $table->char('estatus', 1)->default('P')->comment('P=Pendiente, F=Facturado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_movimientos_prefact');
    }
};
