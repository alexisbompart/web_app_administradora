<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;

class EdificioSeeder extends Seeder
{
    public function run(): void
    {
        // Edificio demo real: CONJ. RESD. LOMA DE LOS MANGOS EDIFICIO 1
        // cod_edif=231, compania Valencia (cod_compania=7)
        $compania = Compania::where('cod_compania', 7)->first();

        if (!$compania) {
            $this->command->error('Compania cod_compania=7 no encontrada. Ejecutar CompaniaSeeder primero.');
            return;
        }

        Edificio::updateOrCreate(
            ['cod_edif' => '231'],
            [
                'compania_id'              => $compania->id,
                'nombre'                   => 'CONJ. RESD. LOMA DE LOS MANGOS EDIFICIO 1',
                'nombre_fiscal'            => 'CONJ. RESD. LOMA DE LOS MANGOS',
                'direccion'                => 'Urb. Loma de los Mangos, Valencia',
                'ciudad'                   => 'Valencia',
                'estado_id'                => 7,
                'total_aptos'              => 102,
                'dia_corte'                => 1,
                'dia_vencimiento'          => 15,
                'mora_porcentaje'          => 5.00,
                'fondo_reserva_porcentaje' => 10.00,
                'honorario_adm'            => 5.00,
                'alicuota_base'            => 0.9804,
                'activo'                   => true,
                'compania_legacy'          => '7',
            ]
        );

        $this->command->info('EdificioSeeder: Edificio demo (cod_edif=231) creado/actualizado.');
    }
}
