<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make edificio_id nullable for imports (file doesn't have COD_EDIF)
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->foreignId('edificio_id')->nullable()->change();
            $table->string('numero_recibo', 50)->nullable()->change();
        });

        // Drop unique on numero_recibo to allow imports
        try {
            Schema::table('cond_pagos', function (Blueprint $table) {
                $table->dropUnique(['numero_recibo']);
            });
        } catch (\Exception $e) {}

        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->string('cajero', 50)->nullable();
            $table->string('cod_motivo', 50)->nullable();
            $table->string('compania_legacy', 20)->nullable();
            $table->string('comprobante_contable', 50)->nullable();
            $table->date('fecha_contable')->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->string('id_pago_legacy', 50)->nullable();
            $table->decimal('monto_num', 15, 2)->nullable();
            $table->string('monto_letra', 500)->nullable();
            $table->string('nro_caja', 20)->nullable();
            $table->decimal('sub_t_efectivo', 15, 2)->nullable();
            $table->decimal('sub_t_efectivo_num', 15, 2)->nullable();
            $table->string('tipo_pago', 10)->nullable();
            $table->decimal('t_abono', 15, 2)->nullable();
            $table->decimal('t_abono_num', 15, 2)->nullable();
            $table->decimal('t_cheque', 15, 2)->nullable();
            $table->decimal('t_cheque_num', 15, 2)->nullable();
            $table->decimal('t_correcpago', 15, 2)->nullable();
            $table->decimal('t_correcpago_num', 15, 2)->nullable();
            $table->decimal('t_deposito', 15, 2)->nullable();
            $table->decimal('t_deposito_num', 15, 2)->nullable();
            $table->decimal('t_dochistoric', 15, 2)->nullable();
            $table->decimal('t_dochistoric_num', 15, 2)->nullable();
            $table->decimal('t_efectivo', 15, 2)->nullable();
            $table->decimal('t_efectivo_num', 15, 2)->nullable();
            $table->decimal('t_tarjeta_credito', 15, 2)->nullable();
            $table->decimal('t_tarjeta_credito_num', 15, 2)->nullable();
            $table->decimal('t_tarjeta_debito', 15, 2)->nullable();
            $table->decimal('t_tarjeta_debito_num', 15, 2)->nullable();
            $table->decimal('t_transferencia', 15, 2)->nullable();
            $table->decimal('t_transferencia_num', 15, 2)->nullable();
            $table->string('legacy_created_by', 100)->nullable();
            $table->timestamp('legacy_created_at')->nullable();
            $table->string('legacy_updated_by', 100)->nullable();
            $table->timestamp('legacy_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cond_pagos', function (Blueprint $table) {
            $table->dropColumn([
                'cajero', 'cod_motivo', 'compania_legacy', 'comprobante_contable',
                'fecha_contable', 'fecha_apertura', 'id_pago_legacy', 'monto_num',
                'monto_letra', 'nro_caja', 'sub_t_efectivo', 'sub_t_efectivo_num',
                'tipo_pago', 't_abono', 't_abono_num', 't_cheque', 't_cheque_num',
                't_correcpago', 't_correcpago_num', 't_deposito', 't_deposito_num',
                't_dochistoric', 't_dochistoric_num', 't_efectivo', 't_efectivo_num',
                't_tarjeta_credito', 't_tarjeta_credito_num', 't_tarjeta_debito',
                't_tarjeta_debito_num', 't_transferencia', 't_transferencia_num',
                'legacy_created_by', 'legacy_created_at', 'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
