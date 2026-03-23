<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cond_descuentos_apto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compania_id')->nullable()->constrained('cond_companias')->nullOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('cond_edificios')->nullOnDelete();
            $table->foreignId('apartamento_id')->nullable()->constrained('cond_aptos')->nullOnDelete();
            $table->string('periodo', 7)->nullable()->comment('YYYY-MM');
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('descuento_num', 15, 2)->nullable();
            $table->decimal('monto_honorario', 15, 2)->nullable();
            $table->decimal('monto_honorario_num', 15, 2)->nullable();
            $table->string('motivo', 50)->nullable();
            $table->text('observaciones')->nullable();
            // Legacy
            $table->string('cod_edif_legacy', 20)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('num_apto_legacy', 20)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cond_descuentos_apto');
    }
};
