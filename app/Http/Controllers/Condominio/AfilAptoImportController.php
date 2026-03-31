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

        $handle = fopen($filePath, 'r');
        $previewRows = [];
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
                    $errors[] = ['line' => $lineNumber, 'info' => '', 'reason' => 'ID legacy vacio o invalido'];
                }
                continue;
            }

            $warnings = [];
            $edificioId = null;
            if ($codEdif && isset($edificios[$codEdif])) {
                $edificioId = $edificios[$codEdif];
            } elseif ($codEdif) {
                $warnings[] = "Edificio '{$codEdif}' no encontrado";
            }

            $apartamentoId = null;
            if ($edificioId && $numApto) {
                $apartamentoId = $apartamentos[$edificioId . '_' . $numApto] ?? null;
                if (!$apartamentoId) $warnings[] = "Apto '{$numApto}' no en edif '{$codEdif}'";
            }

            $companiaId = ($codComp && isset($companias[$codComp])) ? $companias[$codComp] : null;

            $fechaAfiliacion = $this->fastParseDateTime($fechaAfil);

            $mapped = [
                'id'               => (int) $legacyId,
                'cod_pint'         => $codPint,
                'apartamento_id'   => $apartamentoId,
                'edificio_id'      => $edificioId,
                'compania_id'      => $companiaId,
                'estatus_afil'     => $estatusAfil,
                'fecha_afiliacion'  => $fechaAfiliacion,
                'observaciones'    => $obs,
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
                        'warnings'  => $warnings,
                    ],
                ];
            }
        }

        fclose($handle);
        fclose($tempHandle);

        $totalActual = Afilapto::count();

        $summary = [
            'total_archivo'   => $validCount + count($errors),
            'validas'         => $validCount,
            'errores'         => count($errors),
            'total_actual_bd' => $totalActual,
        ];

        return view('condominio.afilapto-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        set_time_limit(600);

        $tempPath = storage_path('app/import_afilapto_' . auth()->id() . '.bin');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.afilapto.importar')
                ->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $results = ['imported' => 0, 'previous_count' => Afilapto::count(), 'errors' => []];

        // Truncar tabla con CASCADE para PostgreSQL (afilpagointegral tiene FK a afilapto)
        try {
            DB::statement('TRUNCATE TABLE afilapto CASCADE');
        } catch (\Exception $e) {
            @unlink($tempPath);
            return redirect()->route('condominio.afilapto.importar')
                ->with('error', 'Error al limpiar tabla: ' . $e->getMessage());
        }

        // Stream e insertar por lotes
        $handle = fopen($tempPath, 'r');
        $batch = [];
        $batchSize = 1000;
        $now = now()->toDateTimeString();
        $columns = ['id', 'cod_pint', 'apartamento_id', 'edificio_id', 'compania_id', 'estatus_afil', 'fecha_afiliacion', 'observaciones', 'created_at', 'updated_at'];

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $data = json_decode($line, true);
                if (!$data) continue;

                $data['created_at'] = $now;
                $data['updated_at'] = $now;

                // Normalizar: solo columnas validas, mantener nulls
                $normalized = [];
                foreach ($columns as $col) {
                    $normalized[$col] = $data[$col] ?? null;
                }
                $batch[] = $normalized;

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
            return redirect()->route('condominio.afilapto.importar')
                ->with('error', 'Error durante la importacion: ' . $e->getMessage());
        }

        fclose($handle);

        // Fix PostgreSQL sequence
        try {
            $maxId = DB::table('afilapto')->max('id') ?? 0;
            DB::statement("SELECT setval(pg_get_serial_sequence('afilapto', 'id'), GREATEST(?, 1), true)", [$maxId]);
        } catch (\Exception $e) {
            // Non-critical
        }

        @unlink($tempPath);
        return view('condominio.afilapto-importar', ['results' => $results]);
    }

    private function insertBatch(array &$batch, array &$results): void
    {
        if (empty($batch)) return;

        // Insertar fila por fila con SAVEPOINT para que un error no aborte todo en PostgreSQL
        foreach ($batch as $row) {
            try {
                DB::statement('SAVEPOINT sp_row');
                DB::table('afilapto')->upsert(
                    [$row],
                    ['id'],
                    ['cod_pint', 'apartamento_id', 'edificio_id', 'compania_id', 'estatus_afil', 'fecha_afiliacion', 'observaciones', 'updated_at']
                );
                DB::statement('RELEASE SAVEPOINT sp_row');
                $results['imported']++;
            } catch (\Exception $e) {
                DB::statement('ROLLBACK TO SAVEPOINT sp_row');
                if (count($results['errors']) < 50) {
                    $results['errors'][] = [
                        'info' => ($row['cod_pint'] ?? '') . '/' . ($row['estatus_afil'] ?? ''),
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
