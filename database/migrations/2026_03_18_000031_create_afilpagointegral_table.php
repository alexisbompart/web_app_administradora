<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afilpagointegral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afilapto_id')->constrained('afilapto')->cascadeOnDelete();
            $table->date('fecha');
            $table->char('letra', 1)->nullable();
            $table->string('cedula_rif', 20);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 100)->nullable();
            $table->string('email_alterno', 100)->nullable();
            $table->string('calle_avenida', 200)->nullable();
            $table->string('piso_apto', 50)->nullable();
            $table->string('edif_casa', 100)->nullable();
            $table->string('urbanizacion', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->foreignId('estado_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->string('telefono', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('otro', 20)->nullable();
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->string('cta_bancaria', 50)->nullable();
            $table->string('tipo_cta', 20)->nullable();
            $table->string('nom_usuario', 50)->nullable();
            $table->string('clave', 50)->nullable();
            $table->string('creado_por', 50)->nullable();
            $table->string('cod_sucursal', 20)->nullable();
            $table->char('estatus', 1)->default('A');
            $table->date('fecha_estatus')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('cedula_rif');
            $table->index('estatus');
            $table->index(['nombres', 'apellidos']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afilpagointegral');
    }
};
