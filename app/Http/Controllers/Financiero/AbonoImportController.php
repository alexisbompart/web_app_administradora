<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondAbonoApto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbonoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'COMPANIA'        => 'compania_legacy',
        'COD_EDIF'        => 'cod_edif_legacy',
        'NUM_APTO'        => 'num_apto_legacy',
        'MES_ANO'         => '_mes_ano',
        'MONTO_ABONO'     => '_monto_abono',
        'MONTO_ABONO#'    => '_monto_abono_num',
        'TIPO_ABONO'      => 'tipo_abono',
        'SERIAL'          => 'serial',
        'FECHA_CANCE'     => 'fecha_cance',
        'CREATED_BY'      => 'legacy_created_by',
        'CREADO'          => 'legacy_created_at',
        'LAST_UPDATE_BY'  => 'legacy_updated_by',
        'MODIFICADO'      => 'legacy_updated_at',
        'OPERACION'       => 'operacion',
    ];

    public function showForm()
    {
        $totalActual = CondAbonoApto::count();
        $ultimaCarga = CondAbonoApto::max('updated_at');
        return view('financiero.abonos-importar', compact('totalActual', 'ultimaCarga'));
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
                if (!$apartamentoId) $rowErrors[] = "Apto '{$numApto}' no existe en edificio '{$codEdif}'";
            }

            $mesAno = $rowData['MES_ANO'] ?? null;
            $periodo = null;
            if ($mesAno) {
                try { $periodo = Carbon::parse($mesAno)->format('Y-m'); }
                catch (\Exception $e) { $rowErrors[] = "MES_ANO invalido"; }
            }

            if (!empty($rowErrors)) {
                $errors[] = ['line' => $lineNumber, 'info' => "{$codEdif}/{$numApto}/{$periodo}", 'reason' => implode(', ', $rowErrors)];
                continue;
            }

            $montoRaw = $rowData['MONTO_ABONO#'] ?? $rowData['MONTO_ABONO'] ?? null;
            $monto = is_numeric($montoRaw) ? (float) $montoRaw : 0;

            $mapped = [
                'compania_id' => $companiaId,
                'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId,
                'fecha' => $this->parseDate($mesAno) ?? now()->toDateString(),
                'periodo' => $periodo,
                'monto' => $monto,
                'monto_abono_num' => is_numeric($rowData['MONTO_ABONO#'] ?? null) ? (float) $rowData['MONTO_ABONO#'] : null,
                'tipo' => $rowData['TIPO_ABONO'] ?? 'abono',
                'tipo_abono' => $rowData['TIPO_ABONO'] ?? null,
                'serial' => $rowData['SERIAL'] ?? null,
                'fecha_cance' => $this->parseDate($rowData['FECHA_CANCE'] ?? null),
                'operacion' => $rowData['OPERACION'] ?? null,
                'cod_edif_legacy' => $codEdif,
                'compania_legacy' => $companiaCode,
                'num_apto_legacy' => $numApto,
                'legacy_created_by' => $rowData['CREATED_BY'] ?? null,
                'legacy_created_at' => $this->parseDateTime($rowData['CREADO'] ?? null),
                'legacy_updated_by' => $rowData['LAST_UPDATE_BY'] ?? null,
                'legacy_updated_at' => $this->parseDateTime($rowData['MODIFICADO'] ?? null),
            ];

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $lineNumber,
                'display' => [
                    'cod_edif' => $codEdif, 'num_apto' => $numApto,
                    'periodo' => $periodo, 'monto' => number_format($monto, 2, ',', '.'),
                    'tipo' => $rowData['TIPO_ABONO'] ?? '', 'serial' => $rowData['SERIAL'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_abonos_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondAbonoApto::count();
        $summary = [
            'total_archivo' => count($rows) + count($errors),
            'validas' => count($rows),
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];
        $previewRows = array_slice($rows, 0, 50);

        return view('financiero.abonos-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tempPath = storage_path('app/import_abonos_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.abonos.importar')->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) { @unlink($tempPath); return redirect()->route('financiero.abonos.importar')->with('error', 'Sin filas validas.'); }

        $results = ['imported' => 0, 'skipped' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondAbonoApto::count();

        $now = now()->toDateTimeString();
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $data = array_filter($row['data'], fn($v) => $v !== null);
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                try {
                    DB::table('cond_abonos_apto')->insert($data);
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
            return redirect()->route('financiero.abonos.importar')->with('error', 'Error en la importacion: ' . $e->getMessage());
        }

        @unlink($tempPath);
        return view('financiero.abonos-importar', ['results' => $results]);
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
