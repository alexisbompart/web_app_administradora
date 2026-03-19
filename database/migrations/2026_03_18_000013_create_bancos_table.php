<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_banco', 10)->unique();
            $table->string('nombre', 100);
            $table->string('iniciales', 10)->nullable();
            $table->string('contacto', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('direccion')->nullable();
            $table->char('estatus_ftp', 1)->default('A');
            $table->string('host_ftp', 100)->nullable();
            $table->string('usuario_ftp', 50)->nullable();
            $table->string('password_ftp', 50)->nullable();
            $table->string('ruta_imagen', 200)->nullable();
            $table->string('ruta_documento', 200)->nullable();
            $table->string('ruta_arch_afil', 200)->nullable();
            $table->string('ruta_arch_pago', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bancos');
    }
};
