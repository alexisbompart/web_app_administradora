<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo\Parametro;

class ParametroSeeder extends Seeder
{
    /**
     * Seed the parametros table with system configuration parameters.
     */
    public function run(): void
    {
        $parametros = [
            // Grupo: impuestos
            [
                'grupo'       => 'impuestos',
                'clave'       => 'iva_porcentaje',
                'valor'       => '16',
                'descripcion' => 'Porcentaje de IVA aplicable',
            ],
            [
                'grupo'       => 'impuestos',
                'clave'       => 'islr_porcentaje_base',
                'valor'       => '3',
                'descripcion' => 'Porcentaje base de retención de ISLR',
            ],
            [
                'grupo'       => 'impuestos',
                'clave'       => 'islr_ut_valor',
                'valor'       => '9',
                'descripcion' => 'Valor de la Unidad Tributaria en bolívares',
            ],

            // Grupo: cobranza
            [
                'grupo'       => 'cobranza',
                'clave'       => 'mora_porcentaje',
                'valor'       => '5',
                'descripcion' => 'Porcentaje de mora por pago tardío',
            ],
            [
                'grupo'       => 'cobranza',
                'clave'       => 'dias_gracia',
                'valor'       => '5',
                'descripcion' => 'Días de gracia antes de aplicar mora',
            ],
            [
                'grupo'       => 'cobranza',
                'clave'       => 'meses_cobranza_judicial',
                'valor'       => '3',
                'descripcion' => 'Meses de morosidad para iniciar cobranza judicial',
            ],

            // Grupo: nomina
            [
                'grupo'       => 'nomina',
                'clave'       => 'sso_empleado_porcentaje',
                'valor'       => '4',
                'descripcion' => 'Porcentaje de SSO retenido al empleado',
            ],
            [
                'grupo'       => 'nomina',
                'clave'       => 'sso_patronal_porcentaje',
                'valor'       => '12',
                'descripcion' => 'Porcentaje de SSO aporte patronal',
            ],
            [
                'grupo'       => 'nomina',
                'clave'       => 'lph_empleado_porcentaje',
                'valor'       => '1',
                'descripcion' => 'Porcentaje de LPH retenido al empleado',
            ],
            [
                'grupo'       => 'nomina',
                'clave'       => 'lph_patronal_porcentaje',
                'valor'       => '2',
                'descripcion' => 'Porcentaje de LPH aporte patronal',
            ],
            [
                'grupo'       => 'nomina',
                'clave'       => 'bono_alimentacion_diario',
                'valor'       => '15',
                'descripcion' => 'Valor diario del bono de alimentación',
            ],

            // Grupo: sistema
            [
                'grupo'       => 'sistema',
                'clave'       => 'moneda_principal',
                'valor'       => 'VES',
                'descripcion' => 'Moneda principal del sistema',
            ],
            [
                'grupo'       => 'sistema',
                'clave'       => 'moneda_secundaria',
                'valor'       => 'USD',
                'descripcion' => 'Moneda secundaria de referencia',
            ],
            [
                'grupo'       => 'sistema',
                'clave'       => 'empresa_nombre',
                'valor'       => 'Administradora Integral',
                'descripcion' => 'Nombre de la empresa administradora',
            ],
        ];

        foreach ($parametros as $parametro) {
            Parametro::updateOrCreate(
                ['clave' => $parametro['clave']],
                $parametro
            );
        }
    }
}
