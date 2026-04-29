<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AfilAptoImportController extends Controller
{
    public function showForm()
    {
        $totalActual = Afilapto::count();
        $ultimaCarga = Afilapto::max('updated_at');
        return view('condominio.afilapto-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:51200']);
        set_time_limit(300);

        $file = $request->file('archivo');
        $filePath = $file->getRealPath();

        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()
            ->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])
            ->toArray();

        // Cargar mapa legacy afilapto_id → afilpagointegral_id generado al importar afilpagointegral
        $mapPath = storage_path('app/import_afilapto_legacy_map.json');
        $legacyMap = file_exists($mapPath) ? (json_decode(file_get_contents($mapPath), true) ?? []) : [];

        $handle = fopen($filePath, 'r');
        $previewRows = [];
        $omitidos = [];
        $errors = [];
        $lineNumber = 0;
        $validCount = 0;

        $tempPath = storage_path('app/import_afilapto_' . auth()->id() . '.bin');
        $tempHandle = fopen($tempPath, 'w');

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = trim($line);
            if ($line === '') continue;

            $fields = str_getcsv($line, ',', '"');
            if (count($fields) < 6) {
                if (count($errors) < 200) {
                    $errors[] = ['line' => $lineNumber, 'info' => '', 'reason' => 'Menos de 6 columnas'];
                }
                continue;
            }

            $legacyId    = $this->clean($fields[0] ?? null);
            $codPint     = $this->clean($fields[1] ?? null);
            $codEdif     = $this->clean($fields[2] ?? null);
            $numApto     = $this->clean($fields[3] ?? null);
            $codComp     = $this->clean($fields[4] ?? null);
            $estatusAfil = $this->clean($fields[5] ?? null);
            $fechaAfil   = $this->clean($fields[6] ?? null);
            $obs         = $this->clean($fields[7] ?? null);

            if (!$legacyId || !is_numeric($legacyId)) {
                if (count($errors) < 200) {
                    $errors[] = ['line' => $lineNumber, 'info' => $codPint ?? '', 'reason' => 'ID legacy vacio o invalido'];
                }
                continue;
            }

            // --- VALIDAR EDIFICIO: obligatorio ---
            if (!$codEdif) {
                if (count($omitidos) < 500) {
                    $omitidos[] = ['line' => $lineNumber, 'cod_pint' => $codPint, 'cod_edif' => '--', 'num_apto' => $numApto, 'reason' => 'COD_EDIF vacío en el archivo'];
                }
                continue;
            }
            if (!isset($edificios[$codEdif])) {
                if (count($omitidos) < 500) {
                    $omitidos[] = ['line' => $lineNumber, 'cod_pint' => $codPint, 'cod_edif' => $codEdif, 'num_apto' => $numApto, 'reason' => "Edificio '{$codEdif}' no existe en BD"];
                }
                continue;
            }
            $edificioId = $edificios[$codEdif];

            // --- VALIDAR APARTAMENTO: obligatorio ---
            if (!$numApto) {
                if (count($omitidos) < 500) {
                    $omitidos[] = ['line' => $lineNumber, 'cod_pint' => $codPint, 'cod_edif' => $codEdif, 'num_apto' => '--', 'reason' => 'NUM_APTO vacío en el archivo'];
                }
                continue;
            }
            $apartamentoId = $apartamentos[$edificioId . '_' . $numApto] ?? null;
            if (!$apartamentoId) {
                if (count($omitidos) < 500) {
                    $omitidos[] = ['line' => $lineNumber, 'cod_pint' => $codPint, 'cod_edif' => $codEdif, 'num_apto' => $numApto, 'reason' => "Apto '{$numApto}' no existe en edificio '{$codEdif}'"];
                }
                continue;
            }

            $companiaId      = ($codComp && isset($companias[$codComp])) ? $companias[$codComp] : null;
            $fechaAfiliacion = $this->fastParseDateTime($fechaAfil);

            // Vincular al afilpagointegral usando el mapa legacy (legacyId es el id del afiliado en sistema viejo)
            $afilpagointegralId = ($estatusAfil === 'A' && isset($legacyMap[(int)$legacyId])) ? $legacyMap[(int)$legacyId] : null;

            $mapped = [
                'afilpagointegral_id' => $afilpagointegralId,
                'cod_pint'            => $codPint,
                'apartamento_id'      => $apartamentoId,
                'edificio_id'         => $edificioId,
                'compania_id'         => $companiaId,
                'estatus_afil'        => $estatusAfil,
                'fecha_afiliacion'    => $fechaAfiliacion,
                'observaciones'       => $obs,
            ];

            fwrite($tempHandle, json_encode($mapped, JSON_UNESCAPED_UNICODE) . "\n");
            $validCount++;

            if (count($previewRows) < 50) {
                $previewRows[] = [
                    'line' => $lineNumber,
                    'display' => [
                        'legacy_id' => $legacyId,
                        'cod_pint'  => $codPint,
                        'cod_edif'  => $codEdif,
                        'num_apto'  => $numApto,
                        'compania'  => $codComp,
                        'estatus'   => $estatusAfil,
                        'fecha'     => $fechaAfil,
                    ],
                ];
            }
        }

        fclose($handle);
        fclose($tempHandle);

        $omitidosPorRazon = collect($omitidos)
            ->groupBy('reason')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->toArray();

        $totalActual = Afilapto::count();

        $summary = [
            'total_archivo'      => $validCount + count($omitidos) + count($errors),
            'validas'            => $validCount,
            'omitidos'           => count($omitidos),
            'errores'            => count($errors),
            'total_actual_bd'    => $totalActual,
            'omitidos_por_razon' => $omitidosPorRazon,
        ];

        return view('condominio.afilapto-importar', compact('summary', 'previewRows', 'omitidos', 'errors'));
    }

    public function execute(Request $request)
    {
        set_time_limit(600);

        $tempPath = storage_path('app/import_afilapto_' . auth()->id() . '.bin');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.afiliaciones-apto.importar')
                ->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $results = ['inserted' => 0, 'updated' => 0, 'errors' => []];

        $handle = fopen($tempPath, 'r');
        $batch = [];
        $batchSize = 500;
        $now = now()->toDateTimeString();

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $data = json_decode($line, true);
                if (!$data) continue;

                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                $batch[] = $data;

                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch, $results);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $this->insertBatch($batch, $results);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            @unlink($tempPath);
            return redirect()->route('condominio.afiliaciones-apto.importar')
                ->with('error', 'Error durante la importacion: ' . $e->getMessage());
        }

        fclose($handle);
        @unlink($tempPath);
        return view('condominio.afilapto-importar', ['results' => $results]);
    }

    private function insertBatch(array &$batch, array &$results): void
    {
        if (empty($batch)) return;

        // La clave única real es apartamento_id (un apto solo puede tener un registro afilapto)
        $apartamentoIds = array_column($batch, 'apartamento_id');
        $existing = DB::table('afilapto')
            ->whereIn('apartamento_id', $apartamentoIds)
            ->pluck('id', 'apartamento_id')
            ->toArray();

        foreach ($batch as $row) {
            $aptoId = $row['apartamento_id'];
            try {
                DB::statement('SAVEPOINT sp_row');
                if (isset($existing[$aptoId])) {
                    // Actualizar registro existente
                    DB::table('afilapto')->where('apartamento_id', $aptoId)->update([
                        'afilpagointegral_id' => $row['afilpagointegral_id'],
                        'cod_pint'            => $row['cod_pint'],
                        'compania_id'         => $row['compania_id'],
                        'estatus_afil'        => $row['estatus_afil'],
                        'fecha_afiliacion'    => $row['fecha_afiliacion'],
                        'observaciones'       => $row['observaciones'],
                        'updated_at'          => $row['updated_at'],
                    ]);
                    $results['updated']++;
                } else {
                    // Insertar nuevo
                    DB::table('afilapto')->insert($row);
                    $existing[$aptoId] = true;
                    $results['inserted']++;
                }
                DB::statement('RELEASE SAVEPOINT sp_row');
            } catch (\Exception $e) {
                DB::statement('ROLLBACK TO SAVEPOINT sp_row');
                if (count($results['errors']) < 50) {
                    $results['errors'][] = [
                        'info'   => ($row['cod_pint'] ?? '') . ' apto_id=' . $aptoId,
                        'reason' => $e->getMessage(),
                    ];
                }
            }
        }
    }

    private function fastParseDateTime(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        return null;
    }

    private function clean(?string $value): ?string
    {
        if ($value === null || strtoupper(trim($value)) === 'NULL' || trim($value) === '') {
            return null;
        }
        return trim($value);
    }
}
