<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondDeudaApto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeudaImportController extends Controller
{
    use ImportFileParser;

    private array $columnMap = [
        'COD_EDIF'              => 'cod_edif_legacy',
        'COMPANIA'              => 'compania_legacy',
        'CREATED_BY'            => 'legacy_created_by',
        'CREADO'                => 'legacy_created_at',
        'DESCUENTO'             => 'descuento',
        'DESCUENTO#'            => 'descuento_num',
        'DESCUENTO_OLD'         => 'descuento_old',
        'DESCUENTO_OLD#'        => 'descuento_old_num',
        'FECHA_PAG'             => 'fecha_pag',
        'GESTIONES'             => 'gestiones',
        'GESTIONES#'            => 'gestiones_num',
        'GESTIONES_OLD'         => 'gestiones_old',
        'GESTIONES_OLD#'        => 'gestiones_old_num',
        'GEST_CONSOLIDADAS'     => 'gest_consolidadas',
        'GEST_CONSOLIDADAS#'    => 'gest_consolidadas_num',
        'LAST_UPDATE_BY'        => 'legacy_updated_by',
        'MODIFICADO'            => 'legacy_updated_at',
        'MES_ANO'               => '_mes_ano',
        'MONTO_DEUDA'           => '_monto_deuda',
        'MONTO_DEUDA#'          => '_monto_deuda_num',
        'NUM_APTO'              => 'num_apto_legacy',
        'SERIAL'                => 'serial',
        'SERIAL_GD'             => 'serial_gd',
    ];

    private array $decimalFields = [
        'descuento', 'descuento_num', 'descuento_old', 'descuento_old_num',
        'gestiones', 'gestiones_num', 'gestiones_old', 'gestiones_old_num',
        'gest_consolidadas', 'gest_consolidadas_num',
    ];

    public function showForm()
    {
        $totalActual = CondDeudaApto::count();
        $ultimaCarga = CondDeudaApto::max('updated_at');

        return view('financiero.deudas-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:51200']);
        set_time_limit(300);

        $file = $request->file('archivo');
        $filePath = $file->getRealPath();

        // Pre-load lookups
        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()
            ->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])
            ->toArray();

        // Stream file line by line
        $handle = fopen($filePath, 'r');
        $headerLine = fgets($handle);

        if (!mb_check_encoding($headerLine, 'UTF-8')) {
            $headerLine = mb_convert_encoding($headerLine, 'UTF-8', 'Windows-1252');
        }

        $headerFields = $this->parseHeader($headerLine);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        $rows = [];
        $errors = [];
        $lineNumber = 1;
        $validCount = 0;

        // Save temp file for execute step - write as we go (streaming)
        $tempPath = storage_path('app/import_deudas_' . auth()->id() . '.bin');
        $tempHandle = fopen($tempPath, 'w');

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;

            if (!mb_check_encoding($line, 'UTF-8')) {
                $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
            }

            $line = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', trim($line));
            if ($line === '' || $line === "''||''") continue;

            $fields = explode('|', $line);
            $rowData = [];
            $rowErrors = [];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (!isset($headerIndex[$sourceCol])) continue;
                $idx = $headerIndex[$sourceCol];
                $value = isset($fields[$idx]) ? trim($fields[$idx]) : '';
                if ($value === '') $value = null;
                $rowData[$sourceCol] = $value;
            }

            // Resolve FKs
            $companiaCode = $rowData['COMPANIA'] ?? null;
            $codEdif = $rowData['COD_EDIF'] ?? null;
            $numApto = $rowData['NUM_APTO'] ?? null;

            $companiaId = $companiaCode && isset($companias[$companiaCode]) ? $companias[$companiaCode] : null;
            $edificioId = $codEdif && isset($edificios[$codEdif]) ? $edificios[$codEdif] : null;
            $apartamentoId = null;

            if (!$companiaId && $companiaCode) $rowErrors[] = "COMPANIA '{$companiaCode}' no encontrada";
            if (!$edificioId && $codEdif) $rowErrors[] = "COD_EDIF '{$codEdif}' no encontrado";
            if (!$numApto) $rowErrors[] = "NUM_APTO vacio";

            if ($edificioId && $numApto) {
                $apartamentoId = $apartamentos[$edificioId . '_' . $numApto] ?? null;
                if (!$apartamentoId) {
                    $rowErrors[] = "Apto '{$numApto}' no existe en edificio '{$codEdif}'";
                }
            }

            // Parse MES_ANO → periodo
            $mesAno = $rowData['MES_ANO'] ?? null;
            $periodo = null;
            if ($mesAno) {
                $periodo = $this->fastParsePeriodo($mesAno);
                if (!$periodo) $rowErrors[] = "MES_ANO invalido";
            } else {
                $rowErrors[] = "MES_ANO vacio";
            }

            $montoDeudaRaw = $rowData['MONTO_DEUDA#'] ?? $rowData['MONTO_DEUDA'] ?? null;
            $montoDeuda = is_numeric($montoDeudaRaw) ? (float) $montoDeudaRaw : 0;

            if (!empty($rowErrors)) {
                if (count($errors) < 200) {
                    $errors[] = [
                        'line' => $lineNumber,
                        'info' => "{$codEdif}/{$numApto}/{$periodo}",
                        'reason' => implode(', ', $rowErrors),
                    ];
                }
                continue;
            }

            // Build mapped data
            $mapped = [
                'compania_id' => $companiaId,
                'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId,
                'periodo' => $periodo,
                'fecha_emision' => $this->fastParseDate($mesAno),
                'fecha_vencimiento' => $this->fastParseDate($mesAno),
                'monto_original' => $montoDeuda,
                'saldo' => $montoDeuda,
                'estatus' => $montoDeuda > 0 ? 'P' : 'C',
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (str_starts_with($targetCol, '_')) continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->fastParseDateTime($value);
                } elseif ($targetCol === 'fecha_pag') {
                    $mapped[$targetCol] = $this->fastParseDate($value);
                } elseif (in_array($targetCol, $this->decimalFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } else {
                    $mapped[$targetCol] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }

            // Write to temp file (one serialized row per line)
            fwrite($tempHandle, json_encode($mapped, JSON_UNESCAPED_UNICODE) . "\n");
            $validCount++;

            // Collect preview rows (first 50 only)
            if (count($rows) < 50) {
                $rows[] = [
                    'line' => $lineNumber,
                    'display' => [
                        'cod_edif' => $codEdif,
                        'num_apto' => $numApto,
                        'periodo' => $periodo,
                        'monto' => number_format($montoDeuda, 2, ',', '.'),
                        'serial' => $rowData['SERIAL'] ?? '',
                    ],
                ];
            }
        }

        fclose($handle);
        fclose($tempHandle);

        $totalActual = CondDeudaApto::count();

        $summary = [
            'total_archivo' => $validCount + count($errors),
            'validas' => $validCount,
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];

        $previewRows = $rows;

        return view('financiero.deudas-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        set_time_limit(600);

        $tempPath = storage_path('app/import_deudas_' . auth()->id() . '.bin');

        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.deudas.importar')
                ->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $results = ['imported' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondDeudaApto::count();

        // Truncate table
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('cond_deudas_apto')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            @unlink($tempPath);
            return redirect()->route('financiero.deudas.importar')
                ->with('error', 'Error al limpiar tabla: ' . $e->getMessage());
        }

        // Stream from temp file and batch insert
        $handle = fopen($tempPath, 'r');
        $batch = [];
        $batchSize = 1000;
        $now = now()->toDateTimeString();

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $data = json_decode($line, true);
                if (!$data) continue;

                // Remove null values and add timestamps
                $data = array_filter($data, fn($v) => $v !== null);
                $data['created_at'] = $now;
                $data['updated_at'] = $now;

                $batch[] = $data;

                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch, $results);
                    $batch = [];
                }
            }

            // Insert remaining rows
            if (!empty($batch)) {
                $this->insertBatch($batch, $results);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            @unlink($tempPath);
            return redirect()->route('financiero.deudas.importar')
                ->with('error', 'Error durante la importacion: ' . $e->getMessage());
        }

        fclose($handle);
        @unlink($tempPath);

        return view('financiero.deudas-importar', ['results' => $results]);
    }

    /**
     * Insert a batch of rows. All rows must have the same columns for bulk insert.
     */
    private function insertBatch(array &$batch, array &$results): void
    {
        if (empty($batch)) return;

        // Normalize: ensure all rows have the same keys
        $allKeys = [];
        foreach ($batch as $row) {
            foreach (array_keys($row) as $k) {
                $allKeys[$k] = true;
            }
        }
        $allKeys = array_keys($allKeys);

        $normalized = [];
        foreach ($batch as $row) {
            $normalizedRow = [];
            foreach ($allKeys as $key) {
                $normalizedRow[$key] = $row[$key] ?? null;
            }
            $normalized[] = $normalizedRow;
        }

        try {
            DB::table('cond_deudas_apto')->insert($normalized);
            $results['imported'] += count($normalized);
        } catch (\Exception $e) {
            // Fallback: insert one by one to identify bad rows
            foreach ($normalized as $row) {
                try {
                    DB::table('cond_deudas_apto')->insert($row);
                    $results['imported']++;
                } catch (\Exception $e2) {
                    if (count($results['errors']) < 50) {
                        $results['errors'][] = [
                            'info' => ($row['cod_edif_legacy'] ?? '') . '/' . ($row['num_apto_legacy'] ?? ''),
                            'reason' => $e2->getMessage(),
                        ];
                    }
                }
            }
        }
    }

    /**
     * Fast date parsing without Carbon overhead.
     * Supports: Y/m/d, Y-m-d, d/m/Y
     */
    private function fastParseDate(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);

        // Y/m/d or Y-m-d (most common in this dataset)
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        // d/m/Y
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        return null;
    }

    /**
     * Fast datetime parsing without Carbon overhead.
     */
    private function fastParseDateTime(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);

        // Y/m/d H:i or Y/m/d H:i:s
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?/', $value, $m)) {
            return sprintf('%04d-%02d-%02d %02d:%02d:%02d', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6] ?? 0);
        }
        return $this->fastParseDate($value) ? $this->fastParseDate($value) . ' 00:00:00' : null;
    }

    /**
     * Fast periodo parsing: MES_ANO → Y-m
     */
    private function fastParsePeriodo(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);

        // Y/m/d or Y-m-d
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/', $value, $m)) {
            return sprintf('%04d-%02d', $m[1], $m[2]);
        }
        // d/m/Y
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d', $m[3], $m[2]);
        }
        // Y/m only
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})$/', $value, $m)) {
            return sprintf('%04d-%02d', $m[1], $m[2]);
        }
        return null;
    }
}
