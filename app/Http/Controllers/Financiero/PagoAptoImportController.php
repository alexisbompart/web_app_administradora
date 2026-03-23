<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondPagoApto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoAptoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'ABONO_HISTORICO'   => 'abono_historico',
        'ABONO_HISTORICO#'  => 'abono_historico_num',
        'CAJERO'            => 'cajero',
        'COD_EDIF'          => 'cod_edif_legacy',
        'COMPANIA'          => 'compania_legacy',
        'CREATED_BY'        => 'legacy_created_by',
        'CREADO'            => 'legacy_created_at',
        'EXONERACION'       => 'exoneracion',
        'EXONERACION#'      => 'exoneracion_num',
        'FECHA_PAG'         => 'fecha_pag',
        'FEC_APERTURA'      => 'fec_apertura',
        'ID_PAGO'           => 'id_pago_legacy',
        'ID_PAGO_APTO'      => 'id_pago_apto_legacy',
        'LAST_UPDATE_BY'    => 'legacy_updated_by',
        'MODIFICADO'        => 'legacy_updated_at',
        'MESES_A_CANCELAR'  => 'meses_a_cancelar',
        'MONTO_PAGO'        => '_monto_pago',
        'MONTO_PAGO#'       => '_monto_pago_num',
        'NRO_CAJA'          => 'nro_caja',
        'NUM_APTO'          => 'num_apto_legacy',
    ];

    public function showForm()
    {
        $totalActual = CondPagoApto::count();
        $ultimaCarga = CondPagoApto::max('updated_at');
        return view('financiero.pagoapto-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:102400']);
        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $hi = $this->buildHeaderIndex($headerFields);

        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])->toArray();

        $rows = []; $errors = [];

        for ($ln = 2; $ln <= count($lines); $ln++) {
            $line = trim($lines[$ln - 1]);
            if ($line === '' || $line === "''||''") continue;

            $fields = explode('|', $line);
            $rd = []; $re = [];

            foreach ($this->columnMap as $sc => $tc) {
                if (!isset($hi[$sc])) continue;
                $v = isset($fields[$hi[$sc]]) ? trim($fields[$hi[$sc]]) : '';
                $rd[$sc] = $v === '' ? null : $v;
            }

            $cc = $rd['COMPANIA'] ?? null; $ce = $rd['COD_EDIF'] ?? null; $na = $rd['NUM_APTO'] ?? null;
            $cid = $cc && isset($companias[$cc]) ? $companias[$cc] : null;
            $eid = $ce && isset($edificios[$ce]) ? $edificios[$ce] : null;
            $aid = null;

            if (!$eid && $ce) $re[] = "COD_EDIF '{$ce}' no encontrado";
            if (!$na) $re[] = "NUM_APTO vacio";
            if ($eid && $na) {
                $aid = $apartamentos[$eid . '_' . $na] ?? null;
                if (!$aid) $re[] = "Apto '{$na}' no existe en edif '{$ce}'";
            }

            if (!empty($re)) { $errors[] = ['line' => $ln, 'info' => "{$ce}/{$na}", 'reason' => implode(', ', $re)]; continue; }

            $montoRaw = $rd['MONTO_PAGO#'] ?? $rd['MONTO_PAGO'] ?? null;
            $monto = is_numeric($montoRaw) ? (float)$montoRaw : 0;
            $fechaPag = $this->parseDate($rd['FECHA_PAG'] ?? null);
            $periodo = $fechaPag ? Carbon::parse($fechaPag)->format('Y-m') : now()->format('Y-m');

            $mapped = [
                'pago_id' => null, 'compania_id' => $cid, 'edificio_id' => $eid,
                'apartamento_id' => $aid, 'periodo' => $periodo, 'monto_aplicado' => $monto,
                'monto_pago' => is_numeric($rd['MONTO_PAGO'] ?? null) ? (float)$rd['MONTO_PAGO'] : null,
                'monto_pago_num' => is_numeric($rd['MONTO_PAGO#'] ?? null) ? (float)$rd['MONTO_PAGO#'] : null,
            ];

            foreach ($this->columnMap as $sc => $tc) {
                if (str_starts_with($tc, '_')) continue;
                $v = $rd[$sc] ?? null;
                if ($v === null) { $mapped[$tc] = null; continue; }
                if ($tc === 'legacy_created_at' || $tc === 'legacy_updated_at') { $mapped[$tc] = $this->parseDT($v); }
                elseif ($tc === 'fecha_pag' || $tc === 'fec_apertura') { $mapped[$tc] = $this->parseDate($v); }
                elseif (in_array($tc, ['abono_historico','abono_historico_num','exoneracion','exoneracion_num'])) { $mapped[$tc] = is_numeric($v) ? (float)$v : null; }
                elseif ($tc === 'meses_a_cancelar') { $mapped[$tc] = is_numeric($v) ? (int)$v : null; }
                else { $mapped[$tc] = $v; }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $ln,
                'display' => [
                    'cod_edif' => $ce, 'num_apto' => $na, 'monto' => number_format($monto, 2, ',', '.'),
                    'fecha_pag' => $fechaPag ?? '', 'id_pago' => $rd['ID_PAGO'] ?? '', 'cajero' => $rd['CAJERO'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_pagoapto_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondPagoApto::count();
        $summary = ['total_archivo' => count($rows) + count($errors), 'validas' => count($rows), 'errores' => count($errors), 'total_actual_bd' => $totalActual];
        $previewRows = array_slice($rows, 0, 50);
        return view('financiero.pagoapto-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tp = storage_path('app/import_pagoapto_' . auth()->id() . '.json');
        if (!file_exists($tp)) return redirect()->route('financiero.pagoapto.importar')->with('error', 'No hay datos.');
        $rows = json_decode(file_get_contents($tp), true);
        if (empty($rows)) { @unlink($tp); return redirect()->route('financiero.pagoapto.importar')->with('error', 'Sin filas.'); }

        $results = ['imported' => 0, 'previous_count' => 0, 'errors' => []];
        DB::beginTransaction();
        try {
            $results['previous_count'] = CondPagoApto::count();
            DB::table('cond_pago_aptos')->truncate();
            $now = now()->toDateTimeString();
            foreach (array_chunk($rows, 500) as $chunk) {
                $ins = array_map(fn($r) => array_merge($r['data'], ['created_at' => $now, 'updated_at' => $now]), $chunk);
                try { DB::table('cond_pago_aptos')->insert($ins); $results['imported'] += count($ins); }
                catch (\Exception $e) { foreach ($ins as $i) { try { DB::table('cond_pago_aptos')->insert($i); $results['imported']++; } catch (\Exception $e2) { $results['errors'][] = ['info' => ($i['cod_edif_legacy'] ?? '') . '/' . ($i['num_apto_legacy'] ?? ''), 'reason' => $e2->getMessage()]; } } }
            }
            DB::commit();
        } catch (\Exception $e) { DB::rollBack(); @unlink($tp); return redirect()->route('financiero.pagoapto.importar')->with('error', 'Error: ' . $e->getMessage()); }
        @unlink($tp);
        return view('financiero.pagoapto-importar', ['results' => $results]);
    }

    private function parseDT(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d H:i', $v)->toDateTimeString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateTimeString(); } catch (\Exception $e) { return null; } } }
    private function parseDate(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d', $v)->toDateString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateString(); } catch (\Exception $e) { return null; } } }
}
