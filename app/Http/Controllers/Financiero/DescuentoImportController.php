<?php

namespace App\Http\Controllers\Financiero;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use App\Models\Financiero\CondDescuentoApto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescuentoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'COD_EDIF'           => 'cod_edif_legacy',
        'COMPANIA'           => 'compania_legacy',
        'CREATED_BY'         => 'legacy_created_by',
        'CREADO'             => 'legacy_created_at',
        'DESCUENTO'          => 'descuento',
        'DESCUENTO#'         => 'descuento_num',
        'LAST_UPDATE_BY'     => 'legacy_updated_by',
        'MODIFICADO'         => 'legacy_updated_at',
        'MES_ANO'            => '_mes_ano',
        'MONTO_HONORARIO'    => 'monto_honorario',
        'MONTO_HONORARIO#'   => 'monto_honorario_num',
        'MOTIVO'             => 'motivo',
        'NUM_APTO'           => 'num_apto_legacy',
        'OBSERVACIONES'      => 'observaciones',
    ];

    private array $decimalFields = [
        'descuento', 'descuento_num', 'monto_honorario', 'monto_honorario_num',
    ];

    public function showForm()
    {
        $totalActual = CondDescuentoApto::count();
        $ultimaCarga = CondDescuentoApto::max('updated_at');
        return view('financiero.descuentos-importar', compact('totalActual', 'ultimaCarga'));
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

            $mapped = [
                'compania_id' => $companiaId,
                'edificio_id' => $edificioId,
                'apartamento_id' => $apartamentoId,
                'periodo' => $periodo,
            ];

            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (str_starts_with($targetCol, '_')) continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) { $mapped[$targetCol] = null; continue; }

                if ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif (in_array($targetCol, $this->decimalFields)) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } else {
                    $mapped[$targetCol] = $value;
                }
            }

            $descuento = $mapped['descuento'] ?? 0;

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            $rows[] = [
                'line' => $lineNumber,
                'display' => [
                    'cod_edif' => $codEdif, 'num_apto' => $numApto,
                    'periodo' => $periodo, 'descuento' => number_format($descuento, 2, ',', '.'),
                    'motivo' => $rowData['MOTIVO'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_descuentos_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalActual = CondDescuentoApto::count();
        $summary = [
            'total_archivo' => count($rows) + count($errors),
            'validas' => count($rows),
            'errores' => count($errors),
            'total_actual_bd' => $totalActual,
        ];
        $previewRows = array_slice($rows, 0, 50);

        return view('financiero.descuentos-importar', compact('summary', 'previewRows', 'errors'));
    }

    public function execute(Request $request)
    {
        $tempPath = storage_path('app/import_descuentos_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('financiero.descuentos.importar')->with('error', 'No hay datos. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('financiero.descuentos.importar')->with('error', 'Sin filas validas.');
        }

        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'previous_count' => 0, 'errors' => []];
        $results['previous_count'] = CondDescuentoApto::count();

        $now = now()->toDateTimeString();
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $data = array_filter($row['data'], fn($v) => $v !== null);

                $aptoId = $data['apartamento_id'] ?? null;
                $periodo = $data['periodo'] ?? null;

                try {
                    if ($aptoId && $periodo) {
                        $existing = DB::table('cond_descuentos_apto')
                            ->where('apartamento_id', $aptoId)
                            ->where('periodo', $periodo)
                            ->first();

                        if ($existing) {
                            $data['updated_at'] = $now;
                            DB::table('cond_descuentos_apto')
                                ->where('id', $existing->id)
                                ->update($data);
                            $results['updated']++;
                        } else {
                            $data['created_at'] = $now;
                            $data['updated_at'] = $now;
                            DB::table('cond_descuentos_apto')->insert($data);
                            $results['imported']++;
                        }
                    } else {
                        $data['created_at'] = $now;
                        $data['updated_at'] = $now;
                        DB::table('cond_descuentos_apto')->insert($data);
                        $results['imported']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'info' => ($data['cod_edif_legacy'] ?? '') . '/' . ($data['num_apto_legacy'] ?? ''),
                        'reason' => $e->getMessage(),
                    ];
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            @unlink($tempPath);
            return redirect()->route('financiero.descuentos.importar')->with('error', 'Error en la importacion: ' . $e->getMessage());
        }

        @unlink($tempPath);
        return view('financiero.descuentos-importar', ['results' => $results]);
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
}
