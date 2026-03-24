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

        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        // Pre-load lookups
        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()
            ->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])
            ->toArray();

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
                try { $periodo = Carbon::parse($mesAno)->format('Y-m'); }
                catch (\Exception $e) { $rowErrors[] = "MES_ANO invalido"; }
            } else {
                $rowErrors[] = "MES_ANO vacio";
            }

            $montoDeudaRaw = $rowData['MONTO_DEUDA#'] ?? $rowData['MONTO_DEUDA'] ?? null;
            $montoDeuda = is_numeric($montoDeudaRaw) ? (float) $montoDeudaRaw : 0;

            if (!empty($rowErrors)) {
                $errors[] = [
                    'line' => $lineNumber,
                    'info' => "{$codEdif}/{$numApto}/{$periodo}",
                    'reason' => implode(', ', $rowErrors),
                ];
                continue;
            }

            // Build mapped data
            $mapped = [
                'compania_id' => $companiaId,
                'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId,
                'periodo' => $periodo,
                'fecha_emision' => $this->parseDate($mesAno) ?? now()->toDateString(),
                'fecha_vencimiento' => $this->parseDate($mesAno) ?? now()->toDateString(),
                'monto_original' => $montoDeuda,
                'saldo' => $montoDeuda,
                'estatus' => $montoDeuda > 0 ? 'P' : 'C',
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (str_starts_with($targetCol, '_')) continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif ($targetCol === 'fecha_pag') {
                    $mapped[$targetCol] = $this->parseDate($value);
                } elseif (in_array($targetCol, $this->decimalFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } else {
                    $mapped[$targetCol] = $value;
                }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $lineNumber,
                'display' => [
                    'cod_edif' => $codEdif,
                    'num_apto' => $numApto,
                    'periodo' => $periodo,
                    'monto' => number_format($montoDeuda, 2, ',', '.'),
                    'serial' => $rowData['SERIAL'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        // Store valid rows in temp file (session can't handle large arrays)
        $tempPath = storage_path('app/import_deudas_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondDeudaApto::count();

        $summary = [
            'total_archivo' => count($rows) + count($errors),
            'validas' => count($rows),
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];

        // Only pass first 50 rows for preview display
        $previewRows = array_slice($rows, 0, 50);

        return view('financiero.deudas-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tempPath = storage_path('app/import_deudas_' . auth()->id() . '.json');

        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.deudas.importar')
                ->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);

        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('financiero.deudas.importar')
                ->with('error', 'El archivo no contiene filas validas.');
        }

        $results = ['imported' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondDeudaApto::count();

        try {
            DB::table('cond_deudas_apto')->truncate();
        } catch (\Exception $e) {
            @unlink($tempPath ?? ($tp ?? ''));
            return redirect()->route('financiero.deudas.importar')->with('error', 'Error al limpiar tabla: ' . $e->getMessage());
        }

        $now = now()->toDateTimeString();
        foreach ($rows as $row) {
            $data = array_filter($row['data'], fn($v) => $v !== null);
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
            try {
                DB::table('cond_deudas_apto')->insert($data);
                $results['imported']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'info' => ($data['cod_edif_legacy'] ?? '') . '/' . ($data['num_apto_legacy'] ?? ''),
                    'reason' => $e->getMessage(),
                ];
            }
        }

        @unlink($tempPath);

        return view('financiero.deudas-importar', ['results' => $results]);
    }

    private function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString(); }
        catch (\Exception $e) {
            try { return Carbon::parse($value)->toDateTimeString(); }
            catch (\Exception $e) { return null; }
        }
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d', $value)->toDateString(); }
        catch (\Exception $e) {
            try { return Carbon::parse($value)->toDateString(); }
            catch (\Exception $e) { return null; }
        }
    }
}
