<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Apartamento;

class RecibosSeeder extends Seeder
{
    public function run(): void
    {
        // Mapas estables: claves legacy -> IDs actuales de esta BD
        $edifMap = Edificio::whereNotNull('cod_edif')->pluck('id', 'cod_edif')->toArray();
        // (cod_edif_legacy|num_apto_legacy) -> apartamento_id
        $aptoMap = [];
        Apartamento::select('id', 'edificio_id', 'num_apto', 'cod_pint')->cursor()->each(function ($a) use (&$aptoMap, $edifMap) {
            $codEdif = array_search($a->edificio_id, $edifMap);
            if ($codEdif !== false) {
                $aptoMap[$codEdif . '|' . $a->num_apto] = $a->id;
            }
            if ($a->cod_pint) {
                $aptoMap['pint|' . $a->cod_pint] = $a->id;
            }
        });

        $this->loadFactEdif($edifMap);
        $this->loadFactApto($edifMap, $aptoMap);
        $this->loadPrefact($edifMap, $aptoMap);
    }

    private function loadFactEdif(array $edifMap): void
    {
        $data = json_decode(file_get_contents(base_path('database/seeders/data/cond_mov_fact_edif.json')), true);
        DB::statement('TRUNCATE TABLE cond_movs_fact_edif CASCADE');
        DB::statement("SELECT setval('cond_movs_fact_edif_id_seq', 1, false)");

        $rows = []; $skipped = 0;
        foreach ($data as $row) {
            $edifId = !empty($row['cod_edif_legacy']) ? ($edifMap[$row['cod_edif_legacy']] ?? null) : null;
            if (!$edifId) { $skipped++; continue; }
            $row['edificio_id'] = $edifId;
            $row['gasto_id'] = null; // gastos no sembrados en esta BD
            $rows[] = $row;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cond_movs_fact_edif')->insert($chunk);
        }
        if (count($rows)) DB::statement("SELECT setval('cond_movs_fact_edif_id_seq', (SELECT MAX(id) FROM cond_movs_fact_edif))");
        $this->command->info("RecibosSeeder [fact_edif]: " . count($rows) . " insertados, {$skipped} omitidos.");
    }

    private function loadFactApto(array $edifMap, array $aptoMap): void
    {
        $data = json_decode(file_get_contents(base_path('database/seeders/data/cond_mov_fact_apto.json')), true);
        DB::statement('TRUNCATE TABLE cond_movs_fact_apto CASCADE');
        DB::statement("SELECT setval('cond_movs_fact_apto_id_seq', 1, false)");

        $rows = []; $skipped = 0;
        foreach ($data as $row) {
            $edifId = !empty($row['cod_edif_legacy']) ? ($edifMap[$row['cod_edif_legacy']] ?? null) : null;
            $key    = ($row['cod_edif_legacy'] ?? '') . '|' . ($row['num_apto_legacy'] ?? '');
            $aptoId = $aptoMap[$key] ?? null;
            if (!$edifId || !$aptoId) { $skipped++; continue; }
            $row['edificio_id']    = $edifId;
            $row['apartamento_id'] = $aptoId;
            $row['gasto_id']       = null; // gastos no sembrados en esta BD
            $rows[] = $row;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cond_movs_fact_apto')->insert($chunk);
        }
        if (count($rows)) DB::statement("SELECT setval('cond_movs_fact_apto_id_seq', (SELECT MAX(id) FROM cond_movs_fact_apto))");
        $this->command->info("RecibosSeeder [fact_apto]: " . count($rows) . " insertados, {$skipped} omitidos.");
    }

    private function loadPrefact(array $edifMap, array $aptoMap): void
    {
        $data = json_decode(file_get_contents(base_path('database/seeders/data/cond_mov_prefact.json')), true);
        DB::statement('TRUNCATE TABLE cond_movimientos_prefact CASCADE');
        DB::statement("SELECT setval('cond_movimientos_prefact_id_seq', 1, false)");

        $rows = []; $skipped = 0;
        foreach ($data as $row) {
            $edifId = !empty($row['cod_edif_legacy']) ? ($edifMap[$row['cod_edif_legacy']] ?? null) : null;
            if (!$edifId) { $skipped++; continue; }
            $row['edificio_id'] = $edifId;
            $row['gasto_id']    = null; // gastos no sembrados en esta BD
            if (!empty($row['num_apto_legacy'])) {
                $key = $row['cod_edif_legacy'] . '|' . $row['num_apto_legacy'];
                $row['apartamento_id'] = $aptoMap[$key] ?? null;
            }
            $rows[] = $row;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cond_movimientos_prefact')->insert($chunk);
        }
        if (count($rows)) DB::statement("SELECT setval('cond_movimientos_prefact_id_seq', (SELECT MAX(id) FROM cond_movimientos_prefact))");
        $this->command->info("RecibosSeeder [prefact]: " . count($rows) . " insertados, {$skipped} omitidos.");
    }
}
