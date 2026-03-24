<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondGasto;
use App\Models\Financiero\CondMovPrefact;
use Carbon\Carbon;
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

        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])->toArray();
        $gastos = CondGasto::pluck('id', 'cod_gasto')->toArray();

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
                $errors[] = ['line' => $lineNumber, 'info' => "{$codEdif}/{$numApto}/{$codGasto}", 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            $montoRaw = $rowData['MONTO#'] ?? $rowData['MONTO'] ?? null;
            $monto = is_numeric($montoRaw) ? (float) $montoRaw : 0;

            $fechaContable = $this->parseDate($rowData['FECHA_CONTABLE'] ?? null);
            $periodo = $fechaContable ? Carbon::parse($fechaContable)->format('Y-m') : now()->format('Y-m');

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

            // Map all legacy fields
            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (str_starts_with($targetCol, '_')) continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif ($targetCol === 'fecha_contable' || $targetCol === 'fecha_fact') {
                    $mapped[$targetCol] = $this->parseDate($value);
                } elseif ($targetCol === 'cuota' || $targetCol === 'mov_id') {
                    $mapped[$targetCol] = is_numeric($value) ? (int) $value : null;
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
                    'cod_edif' => $codEdif, 'num_apto' => $numApto,
                    'cod_gasto' => $codGasto, 'monto' => number_format($monto, 2, ',', '.'),
                    'origen' => $rowData['ORIGEN'] ?? '', 'mov_id' => $rowData['MOV_ID'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_movprefact_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondMovPrefact::count();
        $summary = [
            'total_archivo' => count($rows) + count($errors),
            'validas' => count($rows),
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];
        $previewRows = array_slice($rows, 0, 50);

        return view('financiero.movprefact-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tempPath = storage_path('app/import_movprefact_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.movprefact.importar')->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) { @unlink($tempPath); return redirect()->route('financiero.movprefact.importar')->with('error', 'Sin filas validas.'); }

        $results = ['imported' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondMovPrefact::count();

        try {
            DB::table('cond_movimientos_prefact')->truncate();
        } catch (\Exception $e) {
            @unlink($tempPath ?? ($tp ?? ''));
            return redirect()->route('financiero.movprefact.importar')->with('error', 'Error al limpiar tabla: ' . $e->getMessage());
        }

        $now = now()->toDateTimeString();
        foreach ($rows as $row) {
            $data = array_filter($row['data'], fn($v) => $v !== null);
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
            try {
                DB::table('cond_movimientos_prefact')->insert($data);
                $results['imported']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'info' => ($data['cod_edif_legacy'] ?? '') . '/' . ($data['num_apto_legacy'] ?? ''),
                    'reason' => $e->getMessage(),
                ];
            }
        }

        @unlink($tempPath);
        return view('financiero.movprefact-importar', ['results' => $results]);
    }

    private function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString(); }
        catch (\Exception $e) { try { return Carbon::parse($value)->toDateTimeString(); } catch (\Exception $e) { return null; } }
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try { return Carbon::createFromFormat('Y/m/d', $value)->toDateString(); }
        catch (\Exception $e) { try { return Carbon::parse($value)->toDateString(); } catch (\Exception $e) { return null; } }
    }
}
