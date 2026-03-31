<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondGasto;
use App\Models\Financiero\CondMovPrefact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovPrefactImportController extends Controller
{
    use ImportFileParser;

    private array $columnMap = [
        'A.AMPL_CONCEPTO'       => 'ampl_concepto',
        'APLICAR_GASTO_ADM'     => 'aplicar_gasto_adm',
        'COD_EDIF'              => 'cod_edif_legacy',
        'COD_GASTO'             => 'cod_gasto_legacy',
        'COD_GRUPO'             => 'cod_grupo',
        'COMPANIA'              => 'compania_legacy',
        'COMPROBANTE_CONTABLE'  => 'comprobante_contable',
        'CONT_DIFER'            => 'cont_difer',
        'CREATED_BY'            => 'legacy_created_by',
        'CREADO'                => 'legacy_created_at',
        'CUOTA'                 => 'cuota',
        'EXT_CONCEPTO'          => 'ext_concepto',
        'EXT_DESCRIPCION'       => 'ext_descripcion',
        'FECHA_CONTABLE'        => 'fecha_contable',
        'FECHA_FACT'            => 'fecha_fact',
        'FONDO_RESERVA'         => 'fondo_reserva',
        'ID_CONVENIO'           => 'id_convenio',
        'ID_FACTURA'            => 'id_factura',
        'ID_FINANCIAMIENTO'     => 'id_financiamiento',
        'ID_FRACCION'           => 'id_fraccion',
        'ID_GASTO_DEP'          => 'id_gasto_dep',
        'ID_MINUTA'             => 'id_minuta',
        'ID_PROV_USADA'         => 'id_prov_usada',
        'LAST_UPDATE_BY'        => 'legacy_updated_by',
        'MODIFICADO'            => 'legacy_updated_at',
        'MONTO'                 => '_monto',
        'MONTO#'                => '_monto_num',
        'MOV_ID'                => 'mov_id',
        'NUM_APTO'              => 'num_apto_legacy',
        'OBSERVACIONES'         => 'observaciones',
        'OBSERVACION_AUDIT'     => 'observacion_audit',
        'ORIGEN'                => 'origen',
        'PROCESADO'             => 'procesado',
        'PROVISION'             => 'provision',
        'RECUPERABLE'           => 'recuperable',
        'TIPO_FACT'             => 'tipo_fact',
        'A.TIPO_GASTO'          => 'tipo_gasto_legacy',
    ];

    public function showForm()
    {
        $totalActual = CondMovPrefact::count();
        $ultimaCarga = CondMovPrefact::max('updated_at');
        return view('financiero.movprefact-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:102400']);
        set_time_limit(300);

        $file = $request->file('archivo');
        $filePath = $file->getRealPath();

        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])->toArray();
        $gastos = CondGasto::pluck('id', 'cod_gasto')->toArray();

        // Stream file
        $handle = fopen($filePath, 'r');
        $headerLine = fgets($handle);
        if (!mb_check_encoding($headerLine, 'UTF-8')) {
            $headerLine = mb_convert_encoding($headerLine, 'UTF-8', 'Windows-1252');
        }
        $headerFields = $this->parseHeader($headerLine);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        $previewRows = [];
        $errors = [];
        $lineNumber = 1;
        $validCount = 0;

        $tempPath = storage_path('app/import_movprefact_' . auth()->id() . '.bin');
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

            $companiaCode = $rowData['COMPANIA'] ?? null;
            $codEdif = $rowData['COD_EDIF'] ?? null;
            $numApto = $rowData['NUM_APTO'] ?? null;
            $codGasto = $rowData['COD_GASTO'] ?? null;

            $companiaId = $companiaCode && isset($companias[$companiaCode]) ? $companias[$companiaCode] : null;
            $edificioId = $codEdif && isset($edificios[$codEdif]) ? $edificios[$codEdif] : null;
            $apartamentoId = null;
            $gastoId = $codGasto && isset($gastos[$codGasto]) ? $gastos[$codGasto] : null;

            if (!$edificioId && $codEdif) $rowErrors[] = "COD_EDIF '{$codEdif}' no encontrado";

            if ($edificioId && $numApto && $numApto !== '0') {
                $apartamentoId = $apartamentos[$edificioId . '_' . $numApto] ?? null;
            }

            if (!empty($rowErrors)) {
                if (count($errors) < 200) {
                    $errors[] = ['line' => $lineNumber, 'info' => "{$codEdif}/{$numApto}/{$codGasto}", 'reason' => implode(', ', $rowErrors)];
                }
                continue;
            }

            $montoRaw = $rowData['MONTO#'] ?? $rowData['MONTO'] ?? null;
            $monto = is_numeric($montoRaw) ? (float) $montoRaw : 0;

            $fechaContable = $this->fastParseDate($rowData['FECHA_CONTABLE'] ?? null);
            $periodo = $fechaContable ? substr($fechaContable, 0, 7) : now()->format('Y-m');

            $mapped = [
                'compania_id' => $companiaId,
                'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId,
                'periodo' => $periodo,
                'gasto_id' => $gastoId,
                'concepto' => $rowData['EXT_DESCRIPCION'] ?? $rowData['EXT_CONCEPTO'] ?? $codGasto ?? 'Sin concepto',
                'monto' => $monto,
                'monto_num' => is_numeric($rowData['MONTO#'] ?? null) ? (float) $rowData['MONTO#'] : null,
                'tipo' => 'D',
                'estatus' => ($rowData['PROCESADO'] ?? 'N') === 'S' ? 'F' : 'P',
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (str_starts_with($targetCol, '_')) continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->fastParseDateTime($value);
                } elseif ($targetCol === 'fecha_contable' || $targetCol === 'fecha_fact') {
                    $mapped[$targetCol] = $this->fastParseDate($value);
                } elseif ($targetCol === 'cuota' || $targetCol === 'mov_id') {
                    $mapped[$targetCol] = is_numeric($value) ? (int) $value : null;
                } else {
                    $mapped[$targetCol] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }

            fwrite($tempHandle, json_encode($mapped, JSON_UNESCAPED_UNICODE) . "\n");
            $validCount++;

            if (count($previewRows) < 50) {
                $previewRows[] = [
                    'line' => $lineNumber,
                    'display' => [
                        'cod_edif' => $codEdif, 'num_apto' => $numApto,
                        'cod_gasto' => $codGasto, 'monto' => number_format($monto, 2, ',', '.'),
                        'origen' => $rowData['ORIGEN'] ?? '', 'mov_id' => $rowData['MOV_ID'] ?? '',
                    ],
                ];
            }
        }

        fclose($handle);
        fclose($tempHandle);

        $totalActual = CondMovPrefact::count();
        $summary = [
            'total_archivo' => $validCount + count($errors),
            'validas' => $validCount,
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];

        return view('financiero.movprefact-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        set_time_limit(600);

        $tempPath = storage_path('app/import_movprefact_' . auth()->id() . '.bin');
        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.movprefact.importar')->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $results = ['imported' => 0, 'skipped' => 0, 'previous_count' => CondMovPrefact::count(), 'errors' => []];

        // Carga incremental: NO truncar, solo agregar nuevos registros
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

                $data = array_filter($data, fn($v) => $v !== null);
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
            return redirect()->route('financiero.movprefact.importar')
                ->with('error', 'Error durante la importacion: ' . $e->getMessage());
        }

        fclose($handle);
        @unlink($tempPath);

        return view('financiero.movprefact-importar', ['results' => $results]);
    }

    private function insertBatch(array &$batch, array &$results): void
    {
        if (empty($batch)) return;

        $allKeys = [];
        foreach ($batch as $row) {
            foreach (array_keys($row) as $k) $allKeys[$k] = true;
        }
        $allKeys = array_keys($allKeys);

        $normalized = [];
        foreach ($batch as $row) {
            $nr = [];
            foreach ($allKeys as $key) $nr[$key] = $row[$key] ?? null;
            $normalized[] = $nr;
        }

        try {
            DB::table('cond_movimientos_prefact')->insert($normalized);
            $results['imported'] += count($normalized);
        } catch (\Exception $e) {
            foreach ($normalized as $row) {
                try {
                    DB::table('cond_movimientos_prefact')->insert($row);
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

    private function fastParseDate(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        return null;
    }

    private function fastParseDateTime(?string $value): ?string
    {
        if (!$value || $value === 'NULL') return null;
        $value = trim($value);
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?/', $value, $m)) {
            return sprintf('%04d-%02d-%02d %02d:%02d:%02d', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6] ?? 0);
        }
        $d = $this->fastParseDate($value);
        return $d ? $d . ' 00:00:00' : null;
    }
}
