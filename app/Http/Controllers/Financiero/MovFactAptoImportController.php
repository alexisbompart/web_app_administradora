<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondMovFactApto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovFactAptoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'A.ADMINISTRADO'    => 'administrado',
        'AGUA'              => 'agua',
        'AGUA#'             => 'agua_num',
        'ALICUOTA'          => 'alicuota',
        'ASOC_VECINO'       => 'asoc_vecino',
        'ASOC_VECINO#'      => 'asoc_vecino_num',
        'CANT_CHQ_DEV'      => 'cant_chq_dev',
        'CHQ_DEV'           => 'chq_dev',
        'CHQ_DEV#'          => 'chq_dev_num',
        'COD_EDIF'          => 'cod_edif_legacy',
        'COD_EDIF_PPAL'     => 'cod_edif_ppal',
        'COMPANIA'          => 'compania_legacy',
        'CONVENIOS'         => 'convenios',
        'CONVENIOS#'        => 'convenios_num',
        'CREATED_BY'        => 'legacy_created_by',
        'CREADO'            => 'legacy_created_at',
        'DEMANDADO'         => 'demandado',
        'DEUDA_MAX'         => 'deuda_max',
        'DEUDA_MIN'         => 'deuda_min',
        'FDO_ESPECIAL'      => 'fdo_especial',
        'FDO_ESPECIAL#'     => 'fdo_especial_num',
        'FECHA_FACT'        => 'fecha_fact',
        'GESTIONES'         => 'gestiones',
        'GESTIONES#'        => 'gestiones_num',
        'HONORARIOS'        => 'honorarios',
        'HONORARIOS#'       => 'honorarios_num',
        'IMPUESTOS'         => 'impuestos',
        'IMPUESTOS#'        => 'impuestos_num',
        'INT_MORA'          => 'int_mora',
        'INT_MORA#'         => 'int_mora_num',
        'LAST_UPDATE_BY'    => 'legacy_updated_by',
        'MODIFICADO'        => 'legacy_updated_at',
        'MES_DEUDA'         => 'mes_deuda',
        'MONTOL_PARCIAL'    => 'montol_parcial',
        'MONTOL_TOTAL'      => 'montol_total',
        'NOMBRE_PROPIETARIO'=> 'nombre_propietario',
        'NRO_CHQ_DEV'       => 'nro_chq_dev',
        'NUM_APTO'          => 'num_apto_legacy',
        'NUM_CONSECUTIVO'   => 'num_consecutivo',
        'OTROS_ABONOS'      => 'otros_abonos',
        'OTROS_ABONOS#'     => 'otros_abonos_num',
        'PAGO_PARCIAL'      => 'pago_parcial',
        'PAGO_PARCIAL#'     => 'pago_parcial_num',
        'PAGO_TOTAL'        => 'pago_total',
        'PAGO_TOTAL#'       => 'pago_total_num',
        'PORC_GESTIONES'    => 'porc_gestiones',
        'PORC_GEST_ADM'     => 'porc_gest_adm',
        'PORC_INT_MORA'     => 'porc_int_mora',
        'SERIAL'            => 'serial',
        'TELEGRAMAS'        => 'telegramas',
        'TELEGRAMAS#'       => 'telegramas_num',
        'TIPO_PAGO'         => 'tipo_pago',
        'TOTAL_NO_COMUN'    => 'total_no_comun',
        'A.TOTAL_NO_COMUN#' => 'total_no_comun_num',
    ];

    private array $decimalFields = [
        'agua', 'agua_num', 'alicuota', 'asoc_vecino', 'asoc_vecino_num',
        'chq_dev', 'chq_dev_num', 'convenios', 'convenios_num',
        'fdo_especial', 'fdo_especial_num', 'gestiones', 'gestiones_num',
        'honorarios', 'honorarios_num', 'impuestos', 'impuestos_num',
        'int_mora', 'int_mora_num', 'otros_abonos', 'otros_abonos_num',
        'pago_parcial', 'pago_parcial_num', 'pago_total', 'pago_total_num',
        'porc_gestiones', 'porc_gest_adm', 'porc_int_mora',
        'telegramas', 'telegramas_num', 'total_no_comun', 'total_no_comun_num',
    ];

    private array $dateFields = ['deuda_max', 'deuda_min', 'fecha_fact'];
    private array $intFields = ['cant_chq_dev', 'mes_deuda', 'num_consecutivo'];

    public function showForm()
    {
        $totalActual = CondMovFactApto::count();
        $ultimaCarga = CondMovFactApto::max('updated_at');
        return view('financiero.movfactapto-importar', compact('totalActual', 'ultimaCarga'));
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

            $companiaId = $companiaCode && isset($companias[$companiaCode]) ? $companias[$companiaCode] : null;
            $edificioId = $codEdif && isset($edificios[$codEdif]) ? $edificios[$codEdif] : null;
            $apartamentoId = null;

            if (!$edificioId && $codEdif) $rowErrors[] = "COD_EDIF '{$codEdif}' no encontrado";
            if (!$numApto) $rowErrors[] = "NUM_APTO vacio";

            if ($edificioId && $numApto) {
                $apartamentoId = $apartamentos[$edificioId . '_' . $numApto] ?? null;
                if (!$apartamentoId) $rowErrors[] = "Apto '{$numApto}' no existe en edif '{$codEdif}'";
            }

            if (!empty($rowErrors)) {
                $errors[] = ['line' => $lineNumber, 'info' => "{$codEdif}/{$numApto}", 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            $fechaFact = $this->parseDate($rowData['FECHA_FACT'] ?? null);
            $periodo = $fechaFact ? Carbon::parse($fechaFact)->format('Y-m') : now()->format('Y-m');
            $pagoTotal = is_numeric($rowData['PAGO_TOTAL#'] ?? $rowData['PAGO_TOTAL'] ?? null)
                ? (float) ($rowData['PAGO_TOTAL#'] ?? $rowData['PAGO_TOTAL']) : 0;

            $mapped = [
                'compania_id' => $companiaId, 'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId, 'periodo' => $periodo,
                'concepto' => 'Facturacion ' . $periodo, 'monto' => $pagoTotal, 'tipo' => 'D',
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif (in_array($targetCol, $this->dateFields)) {
                    $mapped[$targetCol] = $this->parseDate($value);
                } elseif (in_array($targetCol, $this->decimalFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } elseif (in_array($targetCol, $this->intFields)) {
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
                    'propietario' => $rowData['NOMBRE_PROPIETARIO'] ?? '',
                    'pago_total' => number_format($pagoTotal, 2, ',', '.'),
                    'fecha_fact' => $fechaFact ?? '', 'mes_deuda' => $rowData['MES_DEUDA'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_movfactapto_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondMovFactApto::count();
        $summary = [
            'total_archivo' => count($rows) + count($errors),
            'validas' => count($rows), 'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];
        $previewRows = array_slice($rows, 0, 50);

        return view('financiero.movfactapto-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tempPath = storage_path('app/import_movfactapto_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) return redirect()->route('financiero.movfactapto.importar')->with('error', 'No hay datos.');

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) { @unlink($tempPath); return redirect()->route('financiero.movfactapto.importar')->with('error', 'Sin filas.'); }

        $results = ['imported' => 0, 'skipped' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondMovFactApto::count();

        $now = now()->toDateTimeString();
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $data = array_filter($row['data'], fn($v) => $v !== null);
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                try {
                    DB::table('cond_movs_fact_apto')->insert($data);
                    $results['imported']++;
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'duplicate') || str_contains($e->getMessage(), 'Duplicate')) {
                        $results['skipped']++;
                    } else {
                        $results['errors'][] = [
                            'info' => ($data['cod_edif_legacy'] ?? '') . '/' . ($data['num_apto_legacy'] ?? ''),
                            'reason' => $e->getMessage(),
                        ];
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            @unlink($tempPath);
            return redirect()->route('financiero.movfactapto.importar')->with('error', 'Error en la importacion: ' . $e->getMessage());
        }

        @unlink($tempPath);
        return view('financiero.movfactapto-importar', ['results' => $results]);
    }

    private function parseDateTime(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d H:i', $v)->toDateTimeString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateTimeString(); } catch (\Exception $e) { return null; } } }
    private function parseDate(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d', $v)->toDateString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateString(); } catch (\Exception $e) { return null; } } }
}
