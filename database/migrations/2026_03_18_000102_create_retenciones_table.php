<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_proveedor_id')->constrained('facturas_proveedores')->cascadeOnDelete();
            $table->string('tipo', 10)->comment('ISLR, IVA');
            $table->decimal('porcentaje', 5, 2);
            $table->decimal('base_imponible', 15, 2);
            $table->decimal('monto_retenido', 15, 2);
            $table->string('numero_comprobante', 50)->nullable();
            $table->date('fecha_retencion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retenciones');
    }
};
