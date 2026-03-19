<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;

class EdificioSeeder extends Seeder
{
    /**
     * Seed the cond_edificios table with demo buildings.
     */
    public function run(): void
    {
        $compania = Compania::where('cod_compania', 'ADM001')->first();

        if (!$compania) {
            $this->command->error('Compania ADM001 not found. Run CompaniaSeeder first.');
            return;
        }

        $edificios = [
            [
                'cod_edif'                => 'TORRE-A',
                'compania_id'             => $compania->id,
                'nombre'                  => 'Torre Las Acacias',
                'direccion'               => 'Av. Las Acacias, Urb. La Florida, Caracas',
                'ciudad'                  => 'Caracas',
                'estado_id'               => 10,
                'telefono'                => '0212-555-1001',
                'email'                   => 'torrea@integralca.com',
                'total_aptos'             => 20,
                'dia_corte'               => 1,
                'dia_vencimiento'         => 15,
                'mora_porcentaje'         => 5.00,
                'fondo_reserva_porcentaje' => 5.00,
                'activo'                  => true,
            ],
            [
                'cod_edif'                => 'TORRE-B',
                'compania_id'             => $compania->id,
                'nombre'                  => 'Residencias Los Pinos',
                'direccion'               => 'Calle Los Pinos, Urb. El Cafetal, Caracas',
                'ciudad'                  => 'Caracas',
                'estado_id'               => 10,
                'telefono'                => '0212-555-1002',
                'email'                   => 'torreb@integralca.com',
                'total_aptos'             => 12,
                'dia_corte'               => 1,
                'dia_vencimiento'         => 15,
                'mora_porcentaje'         => 3.00,
                'fondo_reserva_porcentaje' => 5.00,
                'activo'                  => true,
            ],
            [
                'cod_edif'                => 'TORRE-C',
                'compania_id'             => $compania->id,
                'nombre'                  => 'Edificio El Rosal',
                'direccion'               => 'Av. Venezuela, El Rosal, Caracas',
                'ciudad'                  => 'Caracas',
                'estado_id'               => 10,
                'telefono'                => '0212-555-1003',
                'email'                   => 'torrec@integralca.com',
                'total_aptos'             => 8,
                'dia_corte'               => 1,
                'dia_vencimiento'         => 15,
                'mora_porcentaje'         => 3.00,
                'fondo_reserva_porcentaje' => 5.00,
                'activo'                  => true,
            ],
        ];

        foreach ($edificios as $edificio) {
            Edificio::updateOrCreate(
                ['cod_edif' => $edificio['cod_edif']],
                $edificio
            );
        }
    }
}
