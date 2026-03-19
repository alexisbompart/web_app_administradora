<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;

class ApartamentoSeeder extends Seeder
{
    /**
     * Seed the cond_aptos table with apartments for each building.
     */
    public function run(): void
    {
        $edificios = [
            'TORRE-A' => ['pisos' => 5, 'por_piso' => 4, 'total' => 20],
            'TORRE-B' => ['pisos' => 3, 'por_piso' => 4, 'total' => 12],
            'TORRE-C' => ['pisos' => 2, 'por_piso' => 4, 'total' => 8],
        ];

        foreach ($edificios as $codEdif => $config) {
            $edificio = Edificio::where('cod_edif', $codEdif)->first();

            if (!$edificio) {
                $this->command->error("Edificio {$codEdif} not found. Run EdificioSeeder first.");
                continue;
            }

            $alicuota = round(100 / $config['total'], 4);

            for ($piso = 1; $piso <= $config['pisos']; $piso++) {
                for ($num = 1; $num <= $config['por_piso']; $num++) {
                    $numApto = "{$piso}-{$num}";

                    Apartamento::updateOrCreate(
                        [
                            'edificio_id' => $edificio->id,
                            'num_apto'    => $numApto,
                        ],
                        [
                            'piso'            => $piso,
                            'area_mts'        => rand(60, 120),
                            'alicuota'        => $alicuota,
                            'habitaciones'    => rand(2, 4),
                            'banos'           => rand(1, 3),
                            'estacionamiento' => (bool) rand(0, 1),
                            'estatus'         => 'A',
                        ]
                    );
                }
            }
        }
    }
}
