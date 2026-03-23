<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Models\Financiero\CondGasto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GastoImportController extends Controller
{
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

        $parsed = $this->parseFile($request->file('archivo'));

        if (empty($parsed['rows'])) {
            return back()->with('error', 'No se encontraron filas validas. Errores: ' . count($parsed['errors']));
        }

        // Store file path for execute (keep original file)
        $storedPath = $request->file('archivo')->store('imports');
        session()->put('import_gastos_path', $storedPath);

        $totalActual = CondGasto::count();
        $summary = [
            'total_archivo' => count($parsed['rows']) + count($parsed['errors']),
            'validas' => count($parsed['rows']),
            'new' => count($parsed['rows']),
            'update' => 0,
            'errores' => count($parsed['errors']),
            'total_actual_bd' => $totalActual,
        ];
        $previewRows = array_slice($parsed['rows'], 0, 50);
        $errors = $parsed['errors'];

        return view('financiero.gastos-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $request->validate(['duplicate_action' => 'required|in:update,skip']);

        // Re-parse from stored file
        $storedPath = session()->get('import_gastos_path');
        if (!$storedPath || !file_exists(storage_path('app/' . $storedPath))) {
            return redirect()->route('financiero.gastos.importar')
                ->with('error', 'Archivo no encontrado. Suba el archivo nuevamente.');
        }

        $fullPath = storage_path('app/' . $storedPath);
        $parsed = $this->parseFile(new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath)));

        if (empty($parsed['rows'])) {
            @unlink($fullPath);
            session()->forget('import_gastos_path');
            return redirect()->route('financiero.gastos.importar')
                ->with('error', 'Sin filas validas al reprocesar.');
        }

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($parsed['rows'] as $row) {
            $data = array_filter($row['data'], fn($v) => $v !== null);
            $codGasto = $data['cod_gasto'] ?? $data['codigo'] ?? '';

            try {
                $existing = CondGasto::where('cod_gasto', $codGasto)->first();

                if ($existing) {
                    if ($duplicateAction === 'update') {
                        unset($data['codigo']);
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
                $results['errors'][] = ['info' => $codGasto, 'reason' => $e->getMessage()];
            }
        }

        @unlink($fullPath);
        session()->forget('import_gastos_path');

        return view('financiero.gastos-importar', ['results' => $results]);
    }

    private function parseFile($file): array
    {
        // Read entire file and convert encoding from Latin1 to UTF8
        $content = file_get_contents($file->getRealPath());
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }
        // Remove invalid UTF-8 characters
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $content);
        $lines = explode("\n", $content);

        if (empty($lines)) return ['rows' => [], 'errors' => [['line' => 0, 'info' => '', 'reason' => 'Archivo vacio']]];

        $headerLine = preg_replace('/^\xEF\xBB\xBF/', '', trim($lines[0]));
        $headerFields = explode('|', $headerLine);
        $headerFields = array_values(array_filter($headerFields, fn($f) => trim($f) !== '' && trim($f) !== "''"));
        $headerFields = array_map('trim', $headerFields);

        $headerIndex = [];
        foreach ($headerFields as $idx => $field) { $headerIndex[$field] = $idx; }

        $rows = [];
        $errors = [];

        for ($lineNumber = 2; $lineNumber <= count($lines); $lineNumber++) {
            $line = trim($lines[$lineNumber - 1]);
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

            $codGasto = $rowData['COD_GASTO'] ?? null;
            $descripcion = $rowData['DESCRIPCION'] ?? null;

            if (!$codGasto) $rowErrors[] = "COD_GASTO vacio";
            if (!$descripcion) $rowErrors[] = "DESCRIPCION vacia";

            if (!empty($rowErrors)) {
                $errors[] = ['line' => $lineNumber, 'info' => $codGasto ?? '--', 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            $mapped = [
                'codigo' => $codGasto,
                'cod_gasto' => $codGasto,
                'descripcion' => $descripcion,
                'activo' => ($rowData['STATUS'] ?? 'A') !== 'I',
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
                    $mapped[$targetCol] = $value;
                }
            }

            $mapped = array_filter($mapped, fn($v) => $v !== null);

            $rows[] = [
                'line' => $lineNumber,
                'display' => [
                    'cod_gasto' => $codGasto,
                    'descripcion' => $descripcion,
                    'tipo_gasto' => $rowData['TIPO_GASTO'] ?? '',
                    'clasificacion' => $rowData['CLASIFICACION'] ?? '',
                    'status' => $rowData['STATUS'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        return ['rows' => $rows, 'errors' => $errors];
    }

    private function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString(); }
        catch (\Exception $e) { try { return Carbon::parse($value)->toDateTimeString(); } catch (\Exception $e) { return null; } }
    }
}
