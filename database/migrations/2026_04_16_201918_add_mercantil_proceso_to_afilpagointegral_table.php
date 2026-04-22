<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columnas para rastrear el proceso de dos pasos de Mercantil:
     *  - tipo_operacion : 'A' = afiliación, 'D' = desafiliación
     *  - mercantil_archivo_enviado : nombre del archivo Mdomi/Mdesdomi generado
     *  - mercantil_fecha_envio     : fecha en que se generó/envió el archivo
     *  - mercantil_estatus_proceso : P=pendiente respuesta, A=aprobado, R=rechazado, N=no aplica
     *  - mercantil_fecha_respuesta : fecha en que llegó la respuesta del banco
     *  - mercantil_cod_respuesta   : código de respuesta del banco (ej. 0074)
     *  - mercantil_mensaje         : mensaje de respuesta del banco
     */
    public function up(): void
    {
        Schema::table('afilpagointegral', function (Blueprint $table) {
            $table->char('tipo_operacion', 1)->default('A')->after('estatus')
                  ->comment('A=Afiliacion, D=Desafiliacion');
            $table->string('mercantil_archivo_enviado', 100)->nullable()->after('tipo_operacion')
                  ->comment('Nombre del archivo Mdomi o Mdesdomi generado');
            $table->date('mercantil_fecha_envio')->nullable()->after('mercantil_archivo_enviado')
                  ->comment('Fecha de generacion y envio del archivo al banco');
            $table->char('mercantil_estatus_proceso', 1)->nullable()->after('mercantil_fecha_envio')
                  ->comment('P=Pendiente respuesta, A=Aprobado, R=Rechazado, N=No aplica');
            $table->date('mercantil_fecha_respuesta')->nullable()->after('mercantil_estatus_proceso')
                  ->comment('Fecha en que se proceso la respuesta del banco');
            $table->string('mercantil_cod_respuesta', 10)->nullable()->after('mercantil_fecha_respuesta')
                  ->comment('Codigo de respuesta del banco Mercantil');
            $table->string('mercantil_mensaje', 200)->nullable()->after('mercantil_cod_respuesta')
                  ->comment('Mensaje de respuesta del banco Mercantil');
        });
    }

    public function down(): void
    {
        Schema::table('afilpagointegral', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_operacion',
                'mercantil_archivo_enviado',
                'mercantil_fecha_envio',
                'mercantil_estatus_proceso',
                'mercantil_fecha_respuesta',
                'mercantil_cod_respuesta',
                'mercantil_mensaje',
            ]);
        });
    }
};
