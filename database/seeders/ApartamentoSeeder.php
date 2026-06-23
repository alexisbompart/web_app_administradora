<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;

class ApartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $edificio = Edificio::where('cod_edif', '231')->first();

        if (!$edificio) {
            $this->command->error('Edificio cod_edif=231 no encontrado. Ejecutar EdificioSeeder primero.');
            return;
        }

        // 102 apartamentos del edificio demo: pisos 01-17, letras A-F
        // cod_pint asignados en orden para propietarios demo (los cod_pint reales del sistema)
        // El cod_edif_legacy en cond_aptos identifica el edificio para RecibosSeeder/DeudasDemoSeeder
        $pisos = range(1, 17);
        $letras = ['A', 'B', 'C', 'D', 'E', 'F'];
        $alicuota = round(100 / 102, 6);

        // Mapa de cod_pint reales para los aptos del edificio demo
        // Extraídos del sistema original; estables entre reinicios de BD
        $codPintMap = [
            '11-A' => '03671', '11-B' => '03672', '11-C' => '03673',
            '11-D' => '03674', '11-E' => '03675', '11-F' => '03676',
            '12-A' => '03677', '12-B' => '03678', '12-C' => '03679',
            '12-D' => '03680', '12-E' => '03681', '12-F' => '03682',
            '13-A' => '03683', '13-B' => '03684', '13-C' => '03685',
            '13-D' => '03686', '13-E' => '03687', '13-F' => '03688',
            '14-A' => '03689', '14-B' => '03690',
        ];

        $count = 0;
        foreach ($pisos as $piso) {
            foreach ($letras as $letra) {
                $numApto = str_pad($piso, 2, '0', STR_PAD_LEFT) . '-' . $letra;
                $codPint = $codPintMap[$numApto] ?? null;

                Apartamento::updateOrCreate(
                    [
                        'edificio_id' => $edificio->id,
                        'num_apto'    => $numApto,
                    ],
                    [
                        'piso'             => $piso,
                        'area_mts'         => 75.00,
                        'alicuota'         => $alicuota,
                        'habitaciones'     => 3,
                        'banos'            => 2,
                        'estacionamiento'  => true,
                        'estatus'          => 'A',
                        'cod_pint'         => $codPint,
                        'cod_edif_legacy'  => '231',
                    ]
                );
                $count++;
            }
        }

        $this->command->info("ApartamentoSeeder: {$count} apartamentos del edificio demo creados/actualizados.");
    }
}
