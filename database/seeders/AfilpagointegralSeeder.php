<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Apartamento;

class AfilpagointegralSeeder extends Seeder
{
    public function run(): void
    {
        $afilData     = json_decode(file_get_contents(base_path('database/seeders/data/afilpagointegral.json')), true);
        $afilaptoData = json_decode(file_get_contents(base_path('database/seeders/data/afilapto.json')), true);

        // Mapas estables (cod_pint y cod_edif no cambian entre reinicios)
        $aptoMap   = Apartamento::pluck('id', 'cod_pint')->toArray();   // cod_pint  -> apartamento_id
        $edifMap   = Edificio::pluck('id', 'cod_edif')->toArray();      // cod_edif  -> edificio_id

        DB::statement('TRUNCATE TABLE afilapto CASCADE');
        DB::statement('TRUNCATE TABLE afilpagointegral CASCADE');
        DB::statement("SELECT setval('afilpagointegral_id_seq', 1, false)");
        DB::statement("SELECT setval('afilapto_id_seq', 1, false)");

        // Insertar afilpagointegral (no tiene FKs a apartamentos/edificios)
        foreach (array_chunk($afilData, 500) as $chunk) {
            DB::table('afilpagointegral')->insert(array_map(function ($row) {
                unset($row['banco'], $row['estado'], $row['afilapto'], $row['afilaptos']);
                return $row;
            }, $chunk));
        }
        DB::statement("SELECT setval('afilpagointegral_id_seq', (SELECT MAX(id) FROM afilpagointegral))");

        // Insertar afilapto resolviendo IDs por cod_pint (estable)
        $rows = [];
        $skipped = 0;
        foreach ($afilaptoData as $row) {
            unset($row['apartamento'], $row['edificio'], $row['compania'], $row['afilpagointegral']);

            // Resolver apartamento_id y edificio_id siempre por cod_pint
            $aptoId = !empty($row['cod_pint']) ? ($aptoMap[$row['cod_pint']] ?? null) : null;

            if (!$aptoId) {
                $skipped++;
                continue; // omitir registros sin apartamento válido en esta BD
            }

            $row['apartamento_id'] = $aptoId;
            $row['edificio_id']    = Apartamento::find($aptoId)?->edificio_id ?? $row['edificio_id'];

            $rows[] = $row;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('afilapto')->insert($chunk);
        }
        DB::statement("SELECT setval('afilapto_id_seq', (SELECT MAX(id) FROM afilapto))");

        $this->command->info("AfilpagointegralSeeder: {$afilData[array_key_last($afilData)]['id']} afiliaciones, " . count($rows) . " afilaptos ({$skipped} omitidos sin apto).");
    }
}
