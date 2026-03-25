<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Financiero\CondGasto;
use Illuminate\Http\Request;

class GastoImportController extends Controller
{
    use ImportFileParser;

    private array $columnMap = [
        'APLICA_IDB'            => 'aplica_idb',
        'CLASIFICACION'         => 'clasificacion',
        'COD_CONTABLE2'         => 'cod_contable2',
        'COD_CONTABLE3'         => 'cod_contable3',
        'COD_GASTO'             => 'cod_gasto',
        'COD_GASTO_NOMINA'      => 'cod_gasto_nomina',
        'COD_GRUPO'             => 'cod_grupo',
        'COD_IMPUESTO'          => 'cod_impuesto',
        'COD_MAESTRO_CONTABLE'  => 'cod_maestro_contable',
        'CREATED_BY'            => 'legacy_created_by',
        'CREADO'                => 'legacy_created_at',
        'CTA_INDIVIDUAL'        => 'cta_individual',
        'CTA_IND_INTERCOMPANIA' => 'cta_ind_intercompania',
        'CUOTAS'                => 'cuotas',
        'DESCRIPCION'           => 'descripcion',
        'DIFERIBLE'             => 'diferible',
        'EMPLEADOS'             => 'empleados',
        'ES_FONDO'              => 'es_fondo',
        'EXENTO'                => 'exento',
        'EXONERABLE'            => 'exonerable',
        'FACTURABLE'            => 'facturable',
        'FONDO'                 => 'fondo',
        'FRACCIONABLE'          => 'fraccionable',
        'GASTO_ALTERNO'         => 'gasto_alterno',
        'IMAGEN'                => 'imagen',
        'IMAGEN_GASTO'          => 'imagen_gasto',
        'ISLR'                  => 'islr',
        'LAST_UPDATE_BY'        => 'legacy_updated_by',
        'MODIFICADO'            => 'legacy_updated_at',
        'PRESUPUESTABLE'        => 'presupuestable',
        'REDONDEAR'             => 'redondear',
        'STATUS'                => '_status',
        'TIPO_CALCULO'          => 'tipo_calculo',
        'TIPO_GASTO'            => 'tipo_gasto',
        'TIPO_NEGOCIO'          => 'tipo_negocio',
        'TRANSFERENCIA'         => 'transferencia',
        'ZONA'                  => 'zona',
    ];

    public function showForm()
    {
        $totalActual = CondGasto::count();
        $ultimaCarga = CondGasto::max('updated_at');
        return view('financiero.gastos-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:102400']);

        $lines = $this->readFileLines($request->file('archivo'));
        if (empty($lines) || trim($lines[0]) === '') {
            return back()->with('error', 'Archivo vacio o sin cabecera.');
        }

        $headerFields = $this->parseHeader($lines[0]);
        $headerIndex  = $this->buildHeaderIndex($headerFields);

        // Debug: show detected columns to help diagnose mismatches
        $detectedColumns = implode(', ', $headerFields);

        $parsed = $this->parseRows($lines, $headerIndex);

        if (empty($parsed['rows'])) {
            // Return to view showing errors + detected columns for diagnosis
            $summary = [
                'total_archivo' => count($parsed['errors']),
                'validas'        => 0,
                'new'            => 0,
                'update'         => 0,
                'errores'        => count($parsed['errors']),
                'total_actual_bd'=> CondGasto::count(),
                'detected_cols'  => $detectedColumns,
            ];
            $previewRows = [];
            $errors = $parsed['errors'];
            return view('financiero.gastos-importar', compact('summary', 'previewRows', 'errors'));
        }

        $storedPath = $request->file('archivo')->store('imports');
        session()->put('import_gastos_path', $storedPath);

        $totalActual = CondGasto::count();
        $summary = [
            'total_archivo'  => count($parsed['rows']) + count($parsed['errors']),
            'validas'        => count($parsed['rows']),
            'new'            => count($parsed['rows']),
            'update'         => 0,
            'errores'        => count($parsed['errors']),
            'total_actual_bd'=> $totalActual,
            'detected_cols'  => $detectedColumns,
        ];
        $previewRows = array_slice($parsed['rows'], 0, 50);
        $errors = $parsed['errors'];

        return view('financiero.gastos-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $request->validate(['duplicate_action' => 'required|in:update,skip']);

        $storedPath = session()->get('import_gastos_path');
        if (!$storedPath || !file_exists(storage_path('app/' . $storedPath))) {
            return redirect()->route('financiero.gastos.importar')
                ->with('error', 'Archivo no encontrado. Suba el archivo nuevamente.');
        }

        $fullPath = storage_path('app/' . $storedPath);
        $tmpFile  = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath));
        $lines = $this->readFileLines($tmpFile);
        $headerIndex = $this->buildHeaderIndex($this->parseHeader($lines[0]));
        $parsed = $this->parseRows($lines, $headerIndex);

        @unlink($fullPath);
        session()->forget('import_gastos_path');

        if (empty($parsed['rows'])) {
            return redirect()->route('financiero.gastos.importar')
                ->with('error', 'Sin filas validas al reprocesar.');
        }

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($parsed['rows'] as $row) {
            $data      = array_filter($row['data'], fn($v) => $v !== null);
            $codGasto  = $data['cod_gasto'] ?? '';
            $tipoGasto = $data['tipo_gasto'] ?? null;

            try {
                $existing = CondGasto::where('cod_gasto', $codGasto)
                    ->where('tipo_gasto', $tipoGasto)
                    ->first();

                if ($existing) {
                    if ($duplicateAction === 'update') {
                        $existing->update($data);
                        $results['updated']++;
                    } else {
                        $results['skipped']++;
                    }
                } else {
                    CondGasto::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = ['info' => $codGasto . '/' . $tipoGasto, 'reason' => $e->getMessage()];
            }
        }

        return view('financiero.gastos-importar', ['results' => $results]);
    }

    // -----------------------------------------------------------------------
    private function parseRows(array $lines, array $headerIndex): array
    {
        $rows   = [];
        $errors = [];

        for ($lineNumber = 2; $lineNumber <= count($lines); $lineNumber++) {
            $line = trim($lines[$lineNumber - 1]);
            if ($line === '' || $line === "''||''" || str_starts_with($line, "''||''")) continue;

            $fields  = explode('|', $line);
            $rowData = [];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (!isset($headerIndex[$sourceCol])) continue;
                $idx   = $headerIndex[$sourceCol];
                $value = isset($fields[$idx]) ? trim($fields[$idx]) : '';
                $rowData[$sourceCol] = ($value === '') ? null : $value;
            }

            $codGasto   = $rowData['COD_GASTO'] ?? null;
            $descripcion = $rowData['DESCRIPCION'] ?? null;

            // Fallback: use cod_gasto as descripcion if missing
            if (!$descripcion && $codGasto) {
                $descripcion = $codGasto;
            }

            $rowErrors = [];
            if (!$codGasto) $rowErrors[] = 'COD_GASTO vacio';

            if (!empty($rowErrors)) {
                $errors[] = ['line' => $lineNumber, 'info' => $codGasto ?? '--', 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            // Build mapped data
            $mapped = [
                'codigo'      => $codGasto,
                'cod_gasto'   => $codGasto,
                'descripcion' => $descripcion,
                'activo'      => ($rowData['STATUS'] ?? 'A') !== 'I',
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if ($targetCol === '_status') continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif ($targetCol === 'cuotas') {
                    $mapped[$targetCol] = is_numeric($value) ? (int) $value : null;
                } else {
                    $mapped[$targetCol] = $this->sanitizeString($value);
                }
            }

            $rows[] = [
                'line'    => $lineNumber,
                'display' => [
                    'cod_gasto'    => $codGasto,
                    'descripcion'  => $descripcion,
                    'tipo_gasto'   => $rowData['TIPO_GASTO'] ?? '',
                    'clasificacion'=> $rowData['CLASIFICACION'] ?? '',
                    'status'       => $rowData['STATUS'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        return ['rows' => $rows, 'errors' => $errors];
    }
}
