<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondMovFactEdif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovFactEdifImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'A.ABONOS_FDO_AGUA'    => 'abonos_fdo_agua',      'ABONOS_FDO_AGUA#'   => 'abonos_fdo_agua_num',
        'ABONOS_FDO_CONT'      => 'abonos_fdo_cont',      'ABONOS_FDO_CONT#'   => 'abonos_fdo_cont_num',
        'ABONOS_FDO_ESP'       => 'abonos_fdo_esp',       'ABONOS_FDO_ESP#'    => 'abonos_fdo_esp_num',
        'ABONOS_FDO_RES'       => 'abonos_fdo_res',       'ABONOS_FDO_RES#'    => 'abonos_fdo_res_num',
        'ABONOS_FDO_SOC'       => 'abonos_fdo_soc',       'ABONOS_FDO_SOC#'    => 'abonos_fdo_soc_num',
        'CARGOS_FDO_AGUA'      => 'cargos_fdo_agua',      'CARGOS_FDO_AGUA#'   => 'cargos_fdo_agua_num',
        'CARGOS_FDO_CONT'      => 'cargos_fdo_cont',      'CARGOS_FDO_CONT#'   => 'cargos_fdo_cont_num',
        'CARGOS_FDO_ESP'       => 'cargos_fdo_esp',       'CARGOS_FDO_ESP#'    => 'cargos_fdo_esp_num',
        'CARGOS_FDO_RES'       => 'cargos_fdo_res',       'CARGOS_FDO_RES#'    => 'cargos_fdo_res_num',
        'CARGOS_FDO_SOC'       => 'cargos_fdo_soc',       'CARGOS_FDO_SOC#'    => 'cargos_fdo_soc_num',
        'COBRANZA_EDIF'        => 'cobranza_edif',        'COBRANZA_EDIF#'     => 'cobranza_edif_num',
        'COD_EDIF'             => 'cod_edif_legacy',
        'COMPANIA'             => 'compania_legacy',
        'CREATED_BY'           => 'legacy_created_by',     'CREADO'             => 'legacy_created_at',
        'DEUDA_ACT_EDIF'       => 'deuda_act_edif',       'DEUDA_ACT_EDIF#'    => 'deuda_act_edif_num',
        'DEUDA_ANT_EDIF'       => 'deuda_ant_edif',       'DEUDA_ANT_EDIF#'    => 'deuda_ant_edif_num',
        'FACTURACION_EDIF'     => 'facturacion_edif',     'FACTURACION_EDIF#'  => 'facturacion_edif_num',
        'FECHA_CALCULO'        => 'fecha_calculo',        'FECHA_FACT'         => 'fecha_fact',
        'INT_FDO_RES'          => 'int_fdo_res',          'INT_FDO_RES#'       => 'int_fdo_res_num',
        'LAST_UPDATE_BY'       => 'legacy_updated_by',     'MODIFICADO'         => 'legacy_updated_at',
        'MONTO_PORC_DEV_INT'   => 'monto_porc_dev_int',   'MONTO_PORC_DEV_INT#'=> 'monto_porc_dev_int_num',
        'PLAZO_GRACIA'         => 'plazo_gracia',         'PORC_DEV_INT'       => 'porc_dev_int',
        'PORC_FDO_RES'         => 'porc_fdo_res',         'RECIBOS_PEND'       => 'recibos_pend',
        'REDONDEO'             => 'redondeo',
        'SDO_ACT_FDO_AGUA'     => 'sdo_act_fdo_agua',     'SDO_ACT_FDO_AGUA#'  => 'sdo_act_fdo_agua_num',
        'SDO_ACT_FDO_CONT'     => 'sdo_act_fdo_cont',     'SDO_ACT_FDO_CONT#'  => 'sdo_act_fdo_cont_num',
        'SDO_ACT_FDO_ESP'      => 'sdo_act_fdo_esp',      'SDO_ACT_FDO_ESP#'   => 'sdo_act_fdo_esp_num',
        'SDO_ACT_FDO_RES'      => 'sdo_act_fdo_res',      'SDO_ACT_FDO_RES#'   => 'sdo_act_fdo_res_num',
        'SDO_ACT_FDO_SOC'      => 'sdo_act_fdo_soc',      'SDO_ACT_FDO_SOC#'   => 'sdo_act_fdo_soc_num',
        'SDO_ANT_FDO_AGUA'     => 'sdo_ant_fdo_agua',     'SDO_ANT_FDO_AGUA#'  => 'sdo_ant_fdo_agua_num',
        'SDO_ANT_FDO_CONT'     => 'sdo_ant_fdo_cont',     'SDO_ANT_FDO_CONT#'  => 'sdo_ant_fdo_cont_num',
        'SDO_ANT_FDO_ESP'      => 'sdo_ant_fdo_esp',      'SDO_ANT_FDO_ESP#'   => 'sdo_ant_fdo_esp_num',
        'SDO_ANT_FDO_RES'      => 'sdo_ant_fdo_res',      'SDO_ANT_FDO_RES#'   => 'sdo_ant_fdo_res_num',
        'SDO_ANT_FDO_SOC'      => 'sdo_ant_fdo_soc',      'A.SDO_ANT_FDO_SOC#' => 'sdo_ant_fdo_soc_num',
    ];

    private array $dateFields = ['fecha_calculo', 'fecha_fact'];
    private array $intFields = ['plazo_gracia', 'recibos_pend'];

    public function showForm()
    {
        $totalActual = CondMovFactEdif::count();
        $ultimaCarga = CondMovFactEdif::max('updated_at');
        return view('financiero.movfactedif-importar', compact('totalActual', 'ultimaCarga'));
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

        $rows = []; $errors = [];

        for ($ln = 2; $ln <= count($lines); $ln++) {
            $line = trim($lines[$ln - 1]);
            if ($line === '' || $line === "''||''") continue;

            $fields = explode('|', $line);
            $rd = []; $re = [];

            foreach ($this->columnMap as $sc => $tc) {
                if (!isset($headerIndex[$sc])) continue;
                $v = isset($fields[$headerIndex[$sc]]) ? trim($fields[$headerIndex[$sc]]) : '';
                $rd[$sc] = $v === '' ? null : $v;
            }

            $cc = $rd['COMPANIA'] ?? null; $ce = $rd['COD_EDIF'] ?? null;
            $cid = $cc && isset($companias[$cc]) ? $companias[$cc] : null;
            $eid = $ce && isset($edificios[$ce]) ? $edificios[$ce] : null;

            if (!$eid && $ce) $re[] = "COD_EDIF '{$ce}' no encontrado";
            if (!empty($re)) { $errors[] = ['line' => $ln, 'info' => $ce ?? '--', 'reason' => implode(', ', $re)]; continue; }

            $fechaFact = $this->parseDate($rd['FECHA_FACT'] ?? null);
            $periodo = $fechaFact ? Carbon::parse($fechaFact)->format('Y-m') : now()->format('Y-m');
            $factEdif = is_numeric($rd['FACTURACION_EDIF#'] ?? $rd['FACTURACION_EDIF'] ?? null)
                ? (float)($rd['FACTURACION_EDIF#'] ?? $rd['FACTURACION_EDIF']) : 0;

            $mapped = [
                'compania_id' => $cid, 'edificio_id' => $eid, 'periodo' => $periodo,
                'concepto' => 'Facturacion Edificio ' . $periodo, 'monto_total' => $factEdif, 'tipo' => 'D',
            ];

            foreach ($this->columnMap as $sc => $tc) {
                $v = $rd[$sc] ?? null;
                if ($v === null) { $mapped[$tc] = null; continue; }
                if ($tc === 'legacy_created_at' || $tc === 'legacy_updated_at') { $mapped[$tc] = $this->parseDT($v); }
                elseif (in_array($tc, $this->dateFields)) { $mapped[$tc] = $this->parseDate($v); }
                elseif (in_array($tc, $this->intFields)) { $mapped[$tc] = is_numeric($v) ? (int)$v : null; }
                elseif ($tc === 'cod_edif_legacy' || $tc === 'compania_legacy' || $tc === 'legacy_created_by' || $tc === 'legacy_updated_by' || $tc === 'redondeo') { $mapped[$tc] = $v; }
                else { $mapped[$tc] = is_numeric($v) ? (float)$v : null; }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $ln,
                'display' => ['cod_edif' => $ce, 'facturacion' => number_format($factEdif, 2, ',', '.'), 'fecha_fact' => $fechaFact ?? '', 'cobranza' => number_format((float)($rd['COBRANZA_EDIF#'] ?? $rd['COBRANZA_EDIF'] ?? 0), 2, ',', '.'), 'deuda_act' => number_format((float)($rd['DEUDA_ACT_EDIF#'] ?? $rd['DEUDA_ACT_EDIF'] ?? 0), 2, ',', '.')],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_movfactedif_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondMovFactEdif::count();
        $summary = ['total_archivo' => count($rows) + count($errors), 'validas' => count($rows), 'errores' => count($errors), 'total_actual_bd' => $totalActual];
        $previewRows = array_slice($rows, 0, 50);
        return view('financiero.movfactedif-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tp = storage_path('app/import_movfactedif_' . auth()->id() . '.json');
        if (!file_exists($tp)) return redirect()->route('financiero.movfactedif.importar')->with('error', 'No hay datos.');
        $rows = json_decode(file_get_contents($tp), true);
        if (empty($rows)) { @unlink($tp); return redirect()->route('financiero.movfactedif.importar')->with('error', 'Sin filas.'); }

        $results = ['imported' => 0, 'skipped' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondMovFactEdif::count();

        $now = now()->toDateTimeString();
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $data = array_filter($row['data'], fn($v) => $v !== null);
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                try {
                    DB::table('cond_movs_fact_edif')->insert($data);
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
            @unlink($tp);
            return redirect()->route('financiero.movfactedif.importar')->with('error', 'Error en la importacion: ' . $e->getMessage());
        }
        @unlink($tp);
        return view('financiero.movfactedif-importar', ['results' => $results]);
    }

    private function parseDT(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d H:i', $v)->toDateTimeString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateTimeString(); } catch (\Exception $e) { return null; } } }
    private function parseDate(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d', $v)->toDateString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateString(); } catch (\Exception $e) { return null; } } }
}
