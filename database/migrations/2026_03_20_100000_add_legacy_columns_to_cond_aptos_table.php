<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cond_aptos', function (Blueprint $table) {
            $table->string('administrado', 200)->nullable()->after('estatus');
            $table->decimal('alicuota_especial', 12, 9)->nullable()->after('administrado');
            $table->string('avenida', 200)->nullable()->after('alicuota_especial');
            $table->string('calle', 200)->nullable()->after('avenida');
            $table->string('cargar_honorario', 2)->nullable()->after('calle');
            $table->string('celular', 20)->nullable()->after('cargar_honorario');
            $table->string('ciudad', 100)->nullable()->after('celular');
            $table->string('cod_edif_legacy', 20)->nullable()->after('ciudad');
            $table->string('cod_pint', 20)->nullable()->after('cod_edif_legacy');
            $table->string('cod_ref', 20)->nullable()->after('cod_pint');
            $table->string('contribuye', 1)->nullable()->after('cod_ref');
            $table->boolean('demandado')->default(false)->after('contribuye');
            $table->string('emision_recibo', 1)->nullable()->after('demandado');
            $table->string('enviar_edo_cta', 2)->nullable()->after('emision_recibo');
            $table->string('fax', 20)->nullable()->after('enviar_edo_cta');
            $table->date('fecha_cumple')->nullable()->after('fax');
            $table->date('fec_ult_consolidacion')->nullable()->after('fecha_cumple');
            $table->string('localidad', 200)->nullable()->after('fec_ult_consolidacion');
            $table->integer('nro_consolidacion')->nullable()->after('localidad');
            $table->text('observacion')->nullable()->after('nro_consolidacion');
            $table->string('pais', 100)->nullable()->after('observacion');
            $table->string('rif', 30)->nullable()->after('pais');
            $table->string('telefono_ofic', 20)->nullable()->after('rif');
            $table->string('tipo_doc', 20)->nullable()->after('telefono_ofic');
            $table->string('tipo_pago', 20)->nullable()->after('tipo_doc');
            $table->string('legacy_created_by', 100)->nullable()->after('tipo_pago');
            $table->timestamp('legacy_created_at')->nullable()->after('legacy_created_by');
            $table->string('legacy_updated_by', 100)->nullable()->after('legacy_created_at');
            $table->timestamp('legacy_updated_at')->nullable()->after('legacy_updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('cond_aptos', function (Blueprint $table) {
            $table->dropColumn([
                'administrado', 'alicuota_especial', 'avenida', 'calle',
                'cargar_honorario', 'celular', 'ciudad', 'cod_edif_legacy',
                'cod_pint', 'cod_ref', 'contribuye', 'demandado',
                'emision_recibo', 'enviar_edo_cta', 'fax', 'fecha_cumple',
                'fec_ult_consolidacion', 'localidad', 'nro_consolidacion',
                'observacion', 'pais', 'rif', 'telefono_ofic', 'tipo_doc',
                'tipo_pago', 'legacy_created_by', 'legacy_created_at',
                'legacy_updated_by', 'legacy_updated_at',
            ]);
        });
    }
};
