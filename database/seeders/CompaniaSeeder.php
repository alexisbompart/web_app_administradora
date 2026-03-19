<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;

class CompaniaSeeder extends Seeder
{
    /**
     * Seed the cond_companias table with demo companies.
     */
    public function run(): void
    {
        $companias = [
            [
                'cod_compania' => 'ADM001',
                'nombre'       => 'Administradora Integral C.A.',
                'rif'          => 'J-12345678-1',
                'direccion'    => 'Av. Francisco de Miranda, Torre Europa, Piso 5, Chacao, Caracas',
                'telefono'     => '0212-555-1000',
                'email'        => 'admin@integralca.com',
                'activo'       => true,
            ],
            [
                'cod_compania' => 'ADM002',
                'nombre'       => 'Gestión Inmobiliaria Caracas',
                'rif'          => 'J-87654321-9',
                'direccion'    => 'Calle Veracruz, Las Mercedes, Caracas',
                'telefono'     => '0212-555-2000',
                'email'        => 'info@gestioncaracas.com',
                'activo'       => true,
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
