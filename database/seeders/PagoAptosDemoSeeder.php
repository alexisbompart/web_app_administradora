<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Apartamento;

class PagoAptosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('database/seeders/data/pago_aptos_demo.json')), true);

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

            $row['edificio_id']    = $edifId;
            $row['apartamento_id'] = $aptoId;
            $rows[] = $row;
        }

        if (empty($rows)) {
            $this->command->warn('PagoAptosDemoSeeder: Sin datos que insertar (edificio demo no encontrado).');
            return;
        }

        $primerEdifId = $rows[0]['edificio_id'];
        if (DB::table('cond_pago_aptos')->where('edificio_id', $primerEdifId)->exists()) {
            $this->command->info('PagoAptosDemoSeeder: omitido (ya hay pagos para el edificio demo).');
            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cond_pago_aptos')->insert($chunk);
        }

        $this->command->info('PagoAptosDemoSeeder: ' . count($rows) . ' pagos demo insertados, ' . $skipped . ' omitidos.');
    }
}
