<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ImportFileParser;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartamentoImportController extends Controller
{
    use ImportFileParser;
    private array $columnMap = [
        'A.ADMINISTRADO'        => 'administrado',
        'ALICUOTA'              => 'alicuota',
        'ALICUOTA_ESPECIAL'     => 'alicuota_especial',
        'AVENIDA'               => 'avenida',
        'CALLE'                 => 'calle',
        'CARGAR_HONORARIO'      => 'cargar_honorario',
        'CELULAR'               => 'celular',
        'CIUDAD'                => 'ciudad',
        'COD_EDIF'              => 'cod_edif_legacy',
        'COD_PINT'              => 'cod_pint',
        'COD_REF'               => 'cod_ref',
        'COMPANIA'              => '_compania',
        'CONTRIBUYE'            => 'contribuye',
        'CREATED_BY'            => 'legacy_created_by',
        'CREADO'                => 'legacy_created_at',
        'DEMANDADO'             => 'demandado',
        'DOCUMENTO'             => 'propietario_cedula',
        'EMAIL'                 => 'propietario_email',
        'EMISION_RECIBO'        => 'emision_recibo',
        'ENVIAR_EDO_CTA'        => 'enviar_edo_cta',
        'FAX'                   => 'fax',
        'FECHA_CUMPLE'          => 'fecha_cumple',
        'FEC_ULT_CONSOLIDACION' => 'fec_ult_consolidacion',
        'LAST_UPDATE_BY'        => 'legacy_updated_by',
        'MODIFICADO'            => 'legacy_updated_at',
        'LOCALIDAD'             => 'localidad',
        'NOMBRE_PROPIETARIO'    => 'propietario_nombre',
        'NRO_CONSOLIDACION'     => 'nro_consolidacion',
        'NUM_APTO'              => 'num_apto',
        'OBSERVACION'           => 'observacion',
        'PAIS'                  => 'pais',
        'RIF'                   => 'rif',
        'STATUS'                => 'estatus',
        'TELEFONO'              => 'propietario_telefono',
        'TELEFONO_OFIC'         => 'telefono_ofic',
        'TIPO_DOC'              => 'tipo_doc',
        'A.TIPO_PAGO'           => 'tipo_pago',
    ];

    public function showForm()
    {
        return view('condominio.apartamentos-importar');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $file = $request->file('archivo');
        $lines = $this->readFileLines($file);
        $headerFields = $this->parseHeader($lines[0]);
        $headerIndex = $this->buildHeaderIndex($headerFields);

        // Pre-load edificios — clave "cod_edif|cod_compania" para distinguir mismo número en distinta compañía
        $edificiosRaw = Edificio::with('compania')->get(['id', 'cod_edif', 'compania_id']);
        // Mapa por "cod_edif|cod_compania" => edificio_id
        $edificiosPorEdifCompania = $edificiosRaw
            ->filter(fn($e) => $e->compania)
            ->keyBy(fn($e) => $e->cod_edif . '|' . $e->compania->cod_compania)
            ->map(fn($e) => $e->id)
            ->toArray();
        // Mapa simple por cod_edif => [lista de edificio_ids] para detectar ambigüedad
        $edificiosPorCod = $edificiosRaw
            ->groupBy('cod_edif')
            ->map(fn($group) => $group->pluck('id')->toArray())
            ->toArray();

        // Pre-load existing apartments for duplicate detection
        $existingAptos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()
            ->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])
            ->toArray();

        $rows = [];

        for ($lineNumber = 2; $lineNumber <= count($lines); $lineNumber++) {
            $line = trim($lines[$lineNumber - 1]);
            if ($line === '' || $line === "''||''") continue;

            $fields = explode('|', $line);

            $rowData = [];
            $errors = [];

            // Map fields to columns
            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if (!isset($headerIndex[$sourceCol])) continue;
                $idx = $headerIndex[$sourceCol];
                $value = isset($fields[$idx]) ? trim($fields[$idx]) : '';
                if ($value === '') $value = null;
                $rowData[$sourceCol] = $value;
            }

            // Resolve edificio_id usando COD_EDIF + COMPANIA para distinguir mismo número en distinta compañía
            $codEdif    = $rowData['COD_EDIF']   ?? null;
            $codCompania = $rowData['COMPANIA']  ?? null;
            $edificioId = null;

            if (!$codEdif) {
                $errors[] = "COD_EDIF vacio";
            } elseif ($codCompania && isset($edificiosPorEdifCompania[$codEdif . '|' . $codCompania])) {
                // Coincidencia exacta por cod_edif + compania
                $edificioId = $edificiosPorEdifCompania[$codEdif . '|' . $codCompania];
            } elseif (!$codCompania && isset($edificiosPorCod[$codEdif])) {
                $ids = $edificiosPorCod[$codEdif];
                if (count($ids) === 1) {
                    // Solo un edificio con ese código — sin ambigüedad
                    $edificioId = $ids[0];
                } else {
                    $errors[] = "COD_EDIF '{$codEdif}' existe en " . count($ids) . " compañías distintas y el campo COMPANIA está vacío";
                }
            } else {
                $errors[] = "COD_EDIF '{$codEdif}'" . ($codCompania ? " con COMPANIA '{$codCompania}'" : '') . " no encontrado en edificios";
            }

            $numApto = $rowData['NUM_APTO'] ?? null;
            if (!$numApto) {
                $errors[] = "NUM_APTO vacio";
            }

            // Determine status
            $status = 'error';
            $existingId = null;
            if (empty($errors) && $edificioId && $numApto) {
                $key = $edificioId . '_' . $numApto;
                if (isset($existingAptos[$key])) {
                    $status = 'update';
                    $existingId = $existingAptos[$key];
                } else {
                    $status = 'new';
                }
            }

            // Build mapped data for DB
            $mapped = ['edificio_id' => $edificioId];
            foreach ($this->columnMap as $sourceCol => $targetCol) {
                if ($targetCol === '_compania') continue;
                $value = $rowData[$sourceCol] ?? null;
                if ($value === null) {
                    $mapped[$targetCol] = null;
                    continue;
                }

                // Type conversions
                if ($targetCol === 'demandado') {
                    $mapped[$targetCol] = in_array($value, ['1', 'S', 'SI', 'true'], true);
                } elseif ($targetCol === 'legacy_created_at' || $targetCol === 'legacy_updated_at') {
                    $mapped[$targetCol] = $this->parseDateTime($value);
                } elseif ($targetCol === 'fecha_cumple' || $targetCol === 'fec_ult_consolidacion') {
                    $mapped[$targetCol] = $this->parseDate($value);
                } elseif (in_array($targetCol, ['alicuota', 'alicuota_especial'])) {
                    $mapped[$targetCol] = is_numeric($value) ? (float) $value : null;
                } elseif ($targetCol === 'nro_consolidacion') {
                    $mapped[$targetCol] = is_numeric($value) ? (int) $value : null;
                } else {
                    $mapped[$targetCol] = $value;
                }
            }

            foreach ($mapped as $key => $val) {
                if (is_string($val)) $mapped[$key] = $this->sanitizeString($val);
            }

            // Remove null values for fields that have DB defaults
            $mapped = array_filter($mapped, fn($v) => $v !== null);

            $rows[] = [
                'line' => $lineNumber,
                'status' => $status,
                'errors' => $errors,
                'existing_id' => $existingId,
                'display' => [
                    'cod_edif'   => $codEdif,
                    'compania'   => $codCompania ?? '',
                    'num_apto'   => $numApto,
                    'nombre'     => $rowData['NOMBRE_PROPIETARIO'] ?? '',
                    'alicuota'   => $rowData['ALICUOTA'] ?? '',
                    'email'      => $rowData['EMAIL'] ?? '',
                    'estatus'    => $rowData['STATUS'] ?? '',
                ],
                'data' => $mapped,
            ];
        }

        // Store in session
        session()->put('import_preview', $rows);

        // Store in temp file instead of session
        $tempPath = storage_path('app/import_aptos_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $summary = [
            'total' => count($rows),
            'new' => collect($rows)->where('status', 'new')->count(),
            'update' => collect($rows)->where('status', 'update')->count(),
            'error' => collect($rows)->where('status', 'error')->count(),
        ];

        return view('condominio.apartamentos-importar', compact('rows', 'summary'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:update,skip',
        ]);

        $tempPath = storage_path('app/import_aptos_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.apartamentos.importar')
                ->with('error', 'No hay datos para importar. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('condominio.apartamentos.importar')
                ->with('error', 'Sin filas validas.');
        }

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($rows as $row) {
            if ($row['status'] === 'error') {
                $results['errors'][] = [
                    'line'     => $row['line'],
                    'cod_edif' => $row['display']['cod_edif'] ?? '',
                    'num_apto' => $row['display']['num_apto'] ?? '',
                    'reason'   => implode(', ', $row['errors']),
                ];
                continue;
            }

            $data = array_filter($row['data'], fn($v) => $v !== null);
            $edificioId = $data['edificio_id'] ?? null;
            $numApto = $data['num_apto'] ?? null;

            if (!$edificioId || !$numApto) continue;

            try {
                $existing = Apartamento::withTrashed()
                    ->where('edificio_id', $edificioId)
                    ->where('num_apto', $numApto)
                    ->first();

                if ($existing) {
                    if ($duplicateAction === 'update') {
                        $updateData = $data;
                        unset($updateData['edificio_id'], $updateData['num_apto']);
                        if ($existing->trashed()) $existing->restore();
                        $existing->update($updateData);
                        $results['updated']++;
                    } else {
                        $results['skipped']++;
                    }
                } else {
                    Apartamento::create($data);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'line'     => $row['line'],
                    'cod_edif' => $row['display']['cod_edif'] ?? '',
                    'num_apto' => $numApto ?? '',
                    'reason'   => $e->getMessage(),
                ];
            }
        }

        @unlink($tempPath);
        return view('condominio.apartamentos-importar', ['results' => $results]);
    }

    private function parseDateTime(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return Carbon::createFromFormat('Y/m/d H:i', $value)->toDateTimeString();
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->toDateTimeString();
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return Carbon::createFromFormat('j/n/Y', $value)->toDateString();
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->toDateString();
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
