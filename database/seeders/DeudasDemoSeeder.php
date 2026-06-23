<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Apartamento;

class DeudasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('database/seeders/data/deudas_demo.json')), true);

        $edifMap = Edificio::whereNotNull('cod_edif')->pluck('id', 'cod_edif')->toArray();
        $aptoMap = [];
        Apartamento::select('id', 'edificio_id', 'num_apto')->cursor()->each(function ($a) use (&$aptoMap, $edifMap) {
            $codEdif = array_search($a->edificio_id, $edifMap);
            if ($codEdif !== false) {
                $aptoMap[$codEdif . '|' . $a->num_apto] = $a->id;
            }
        });

        $rows = [];
        $skipped = 0;
        foreach ($data as $row) {
            $edifId = !empty($row['cod_edif_legacy']) ? ($edifMap[$row['cod_edif_legacy']] ?? null) : null;
            $key    = ($row['cod_edif_legacy'] ?? '') . '|' . ($row['num_apto_legacy'] ?? '');
            $aptoId = !empty($key) ? ($aptoMap[$key] ?? null) : null;

            if (!$edifId || !$aptoId) {
                $skipped++;
                continue;
            }

            // Omitir si ya hay deuda con mismo edificio/apto/periodo
            $row['edificio_id']    = $edifId;
            $row['apartamento_id'] = $aptoId;
            $rows[] = $row;
        }

        if (empty($rows)) {
            $this->command->warn('DeudasDemoSeeder: Sin datos que insertar (edificio demo no encontrado o ya existen deudas).');
            return;
        }

        // Verificar si ya existen deudas para este edificio
        $primerEdifId = $rows[0]['edificio_id'];
        if (DB::table('cond_deudas_apto')->where('edificio_id', $primerEdifId)->exists()) {
            $this->command->info('DeudasDemoSeeder: omitido (ya hay deudas para el edificio demo).');
            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cond_deudas_apto')->insert($chunk);
        }

        $this->command->info('DeudasDemoSeeder: ' . count($rows) . ' deudas demo insertadas, ' . $skipped . ' omitidas.');
    }
}
