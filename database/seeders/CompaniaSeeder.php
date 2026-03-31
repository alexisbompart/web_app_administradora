<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;

class CompaniaSeeder extends Seeder
{
    public function run(): void
    {
        $companias = [
            [
                'cod_compania' => '1',
                'nombre'       => 'Administradora Integral (Caracas)',
                'rif'          => 'J-30840370-9',
                'direccion'    => 'Av. Las Mercedes y Calle Guaicaipuro, Edif. Torre Forum, PB Local A, El Rosal, Chacao',
                'telefono'     => '(0212) 951-56-11',
                'email'        => 'info@administradoraintegral.com',
                'activo'       => true,
                'latitud'      => 10.4961,
                'longitud'     => -66.8658,
            ],
            [
                'cod_compania' => '7',
                'nombre'       => 'Administradora Integral (Valencia)',
                'rif'          => 'J-29407081-7',
                'direccion'    => 'Valencia, Edo. Carabobo',
                'telefono'     => null,
                'email'        => null,
                'activo'       => true,
                'latitud'      => 10.1800,
                'longitud'     => -67.9900,
            ],
            [
                'cod_compania' => '15',
                'nombre'       => 'Administradora Integral (Caracas Sur)',
                'rif'          => 'J-31033580-0',
                'direccion'    => 'Caracas',
                'telefono'     => null,
                'email'        => null,
                'activo'       => true,
                'latitud'      => 10.4806,
                'longitud'     => -66.9036,
            ],
            [
                'cod_compania' => '19',
                'nombre'       => 'Administradora Integral (Litoral)',
                'rif'          => 'J-31446460-4',
                'direccion'    => 'Caraballeda, Edo. La Guaira',
                'telefono'     => null,
                'email'        => null,
                'activo'       => true,
                'latitud'      => 10.6100,
                'longitud'     => -66.8500,
            ],
        ];

        foreach ($companias as $compania) {
            Compania::updateOrCreate(
                ['cod_compania' => $compania['cod_compania']],
                $compania
            );
        }
    }
}
