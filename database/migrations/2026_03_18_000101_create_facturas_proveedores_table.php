<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $table->foreignId('compania_id')->constrained('cond_companias')->cascadeOnDelete();
            $table->string('numero_factura', 50);
            $table->string('numero_control', 50)->nullable();
            $table->date('fecha_factura');
            $table->date('fecha_recepcion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('base_imponible', 15, 2)->default(0);
            $table->decimal('monto_exento', 15, 2)->default(0);
            $table->decimal('iva_porcentaje', 5, 2)->default(16);
            $table->decimal('iva_monto', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->string('estatus', 20)->default('pendiente')->comment('pendiente, aprobada, pagada, anulada');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['proveedor_id', 'numero_factura']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_proveedores');
    }
};
