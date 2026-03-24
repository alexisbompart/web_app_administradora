<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Compania;
use App\Models\Financiero\CondPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'CAJERO'                => 'cajero',
        'COD_MOTIVO'            => 'cod_motivo',
        'COMPANIA'              => 'compania_legacy',
        'COMPROBANTE_CONTABLE'  => 'comprobante_contable',
        'CREATED_BY'            => 'legacy_created_by',
        'CREADO'                => 'legacy_created_at',
        'ESTATUS'               => '_estatus',
        'FECHA_CONTABLE'        => 'fecha_contable',
        'FECHA_PAGO'            => '_fecha_pago',
        'FECHA_APERTURA'        => 'fecha_apertura',
        'ID_PAGO'               => 'id_pago_legacy',
        'LAST_UPDATE_BY'        => 'legacy_updated_by',
        'MODIFICADO'            => 'legacy_updated_at',
        'MONTO'                 => '_monto',
        'MONTO#'                => '_monto_num',
        'MONTO_LETRA'           => 'monto_letra',
        'NRO_CAJA'              => 'nro_caja',
        'SUB_T_EFECTIVO'        => 'sub_t_efectivo',
        'SUB_T_EFECTIVO#'       => 'sub_t_efectivo_num',
        'TIPO_PAGO'             => 'tipo_pago',
        'T_ABONO'               => 't_abono',
        'T_ABONO#'              => 't_abono_num',
        'T_CHEQUE'              => 't_cheque',
        'T_CHEQUE#'             => 't_cheque_num',
        'T_CORRECPAGO'          => 't_correcpago',
        'T_CORRECPAGO#'         => 't_correcpago_num',
        'T_DEPOSITO'            => 't_deposito',
        'T_DEPOSITO#'           => 't_deposito_num',
        'T_DOCHISTORIC'         => 't_dochistoric',
        'T_DOCHISTORIC#'        => 't_dochistoric_num',
        'T_EFECTIVO'            => 't_efectivo',
        'T_EFECTIVO#'           => 't_efectivo_num',
        'T_TARJETA_CREDITO'     => 't_tarjeta_credito',
        'T_TARJETA_CREDITO#'    => 't_tarjeta_credito_num',
        'T_TARJETA_DEBITO'      => 't_tarjeta_debito',
        'T_TARJETA_DEBITO#'     => 't_tarjeta_debito_num',
        'T_TRANSFERENCIA'       => 't_transferencia',
        'T_TRANSFERENCIA#'      => 't_transferencia_num',
    ];

    private array $decimalFields = [
        'sub_t_efectivo', 'sub_t_efectivo_num', 't_abono', 't_abono_num',
        't_cheque', 't_cheque_num', 't_correcpago', 't_correcpago_num',
        't_deposito', 't_deposito_num', 't_dochistoric', 't_dochistoric_num',
        't_efectivo', 't_efectivo_num', 't_tarjeta_credito', 't_tarjeta_credito_num',
        't_tarjeta_debito', 't_tarjeta_debito_num', 't_transferencia', 't_transferencia_num',
    ];

    public function showForm()
    {
        $totalActual = CondPago::count();
        $ultimaCarga = CondPago::max('updated_at');
        return view('financiero.pagos-importar', compact('totalActual', 'ultimaCarga'));
    }

    public function preview(Request $request)
    {
        $request->validate(['archivo' => 'required|file|max:102400']);
        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $hi = $this->buildHeaderIndex($headerFields);

        $companias = Compania::pluck('id', 'cod_compania')->toArray();

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

            $cc = $rd['COMPANIA'] ?? null;
            $cid = $cc && isset($companias[$cc]) ? $companias[$cc] : null;

            $fechaPago = $this->parseDate($rd['FECHA_PAGO'] ?? null);
            if (!$fechaPago) $re[] = "FECHA_PAGO invalida";

            $montoRaw = $rd['MONTO#'] ?? $rd['MONTO'] ?? null;
            $monto = is_numeric($montoRaw) ? (float)$montoRaw : 0;

            if (!empty($re)) { $errors[] = ['line' => $ln, 'info' => $rd['ID_PAGO'] ?? '--', 'reason' => implode(', ', $re)]; continue; }

            // Map TIPO_PAGO to forma_pago
            $tipoPago = $rd['TIPO_PAGO'] ?? '';
            $formaPago = match(strtoupper($tipoPago)) {
                'CO' => 'pago_integral', 'EF' => 'efectivo', 'CH' => 'cheque',
                'TR' => 'transferencia', 'DE' => 'deposito', 'TC' => 'tarjeta',
                default => 'transferencia',
            };

            $mapped = [
                'compania_id' => $cid, 'edificio_id' => null,
                'fecha_pago' => $fechaPago, 'forma_pago' => $formaPago,
                'monto_total' => $monto, 'monto_recibido' => $monto,
                'estatus' => $rd['ESTATUS'] ?? 'A',
                'numero_recibo' => $rd['ID_PAGO'] ?? null,
            ];

            foreach ($this->columnMap as $sc => $tc) {
                if (str_starts_with($tc, '_')) continue;
                $v = $rd[$sc] ?? null;
                if ($v === null) { $mapped[$tc] = null; continue; }
                if ($tc === 'legacy_created_at' || $tc === 'legacy_updated_at') { $mapped[$tc] = $this->parseDT($v); }
                elseif ($tc === 'fecha_contable' || $tc === 'fecha_apertura') { $mapped[$tc] = $this->parseDate($v); }
                elseif (in_array($tc, $this->decimalFields)) { $mapped[$tc] = is_numeric($v) ? (float)$v : null; }
                else { $mapped[$tc] = $v; }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $ln,
                'display' => [
                    'id_pago' => $rd['ID_PAGO'] ?? '', 'cajero' => $rd['CAJERO'] ?? '',
                    'monto' => number_format($monto, 2, ',', '.'), 'fecha_pago' => $fechaPago,
                    'tipo_pago' => $tipoPago, 'estatus' => $rd['ESTATUS'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_pagos_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondPago::count();
        $summary = ['total_archivo' => count($rows) + count($errors), 'validas' => count($rows), 'errores' => count($errors), 'total_actual_bd' => $totalActual];
        $previewRows = array_slice($rows, 0, 50);
        return view('financiero.pagos-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tp = storage_path('app/import_pagos_' . auth()->id() . '.json');
        if (!file_exists($tp)) return redirect()->route('financiero.pagos.importar')->with('error', 'No hay datos.');
        $rows = json_decode(file_get_contents($tp), true);
        if (empty($rows)) { @unlink($tp); return redirect()->route('financiero.pagos.importar')->with('error', 'Sin filas.'); }

        $results = ['imported' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondPago::withTrashed()->count();

        try {
            DB::statement('SET session_replication_role = replica;');
            DB::table('cond_pagos')->truncate();
            DB::statement('SET session_replication_role = DEFAULT;');
        } catch (\Exception $e) {
            @unlink($tp);
            return redirect()->route('financiero.pagos.importar')->with('error', 'Error al limpiar tabla: ' . $e->getMessage());
        }

        $now = now()->toDateTimeString();
        foreach ($rows as $row) {
            $data = array_filter($row['data'], fn($v) => $v !== null);
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
            try {
                DB::table('cond_pagos')->insert($data);
                $results['imported']++;
            } catch (\Exception $e) {
                $results['errors'][] = ['info' => $data['id_pago_legacy'] ?? '', 'reason' => $e->getMessage()];
            }
        }
        @unlink($tp);
        return view('financiero.pagos-importar', ['results' => $results]);
    }

    private function parseDT(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d H:i', $v)->toDateTimeString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateTimeString(); } catch (\Exception $e) { return null; } } }
    private function parseDate(?string $v): ?string { if (!$v) return null; try { return Carbon::createFromFormat('Y/m/d', $v)->toDateString(); } catch (\Exception $e) { try { return Carbon::parse($v)->toDateString(); } catch (\Exception $e) { return null; } } }
}
