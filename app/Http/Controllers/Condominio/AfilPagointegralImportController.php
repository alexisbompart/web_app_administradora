<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilpagointegral;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AfilPagointegralImportController extends Controller
{
    public function showForm()
    {
        return view('condominio.afilpagointegral-importar');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:51200',
        ]);

        $file    = $request->file('archivo');
        $content = mb_convert_encoding(file_get_contents($file->getRealPath()), 'UTF-8', 'UTF-8');
        $lines   = explode("\n", $content);

        // Pre-cargar cédulas existentes para detección de duplicados
        $existingCedulas = Afilpagointegral::pluck('id', 'cedula_rif')->toArray();

        $rows     = [];
        $omitidos = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $fields = str_getcsv($line, ',', '"');
            if (count($fields) < 27) continue; // 27 sin afilapto_id, 28 con él

            $lineNumber = $index + 1;

            // Auto-detectar si la columna 0 es el afilapto_id legacy (numérico)
            $col0 = trim($fields[0] ?? '');
            $hasLegacyId = (is_numeric($col0) && strlen($col0) < 10);
            $offset = $hasLegacyId ? 1 : 0;
            $legacyAfilAptoId = $hasLegacyId ? (int) $col0 : null;

            $fecha         = $this->clean($fields[$offset + 0]  ?? null);
            $letra         = $this->clean($fields[$offset + 1]  ?? null);
            $cedulaRif     = $this->clean($fields[$offset + 2]  ?? null);
            $nombres       = $this->clean($fields[$offset + 3]  ?? null);
            $apellidos     = $this->clean($fields[$offset + 4]  ?? null);
            $email         = $this->clean($fields[$offset + 5]  ?? null);
            $emailAlt      = $this->clean($fields[$offset + 6]  ?? null);
            $calleAv       = $this->clean($fields[$offset + 7]  ?? null);
            $pisoApto      = $this->clean($fields[$offset + 8]  ?? null);
            $edifCasa      = $this->clean($fields[$offset + 9]  ?? null);
            $urbanizacion  = $this->clean($fields[$offset + 10] ?? null);
            $ciudad        = $this->clean($fields[$offset + 11] ?? null);
            $estadoId      = $this->clean($fields[$offset + 12] ?? null);
            $telefono      = $this->clean($fields[$offset + 13] ?? null);
            $fax           = $this->clean($fields[$offset + 14] ?? null);
            $celular       = $this->clean($fields[$offset + 15] ?? null);
            $otro          = $this->clean($fields[$offset + 16] ?? null);
            $bancoId       = $this->clean($fields[$offset + 17] ?? null);
            $ctaBancaria   = $this->clean($fields[$offset + 18] ?? null);
            $tipoCta       = $this->clean($fields[$offset + 19] ?? null);
            $nomUsuario    = $this->clean($fields[$offset + 20] ?? null);
            $clave         = $this->clean($fields[$offset + 21] ?? null);
            $creadoPor     = $this->clean($fields[$offset + 22] ?? null);
            $codSucursal   = $this->clean($fields[$offset + 23] ?? null);
            $estatus       = $this->clean($fields[$offset + 24] ?? null);
            $fechaEstatus  = $this->clean($fields[$offset + 25] ?? null);
            $observaciones = $this->clean($fields[$offset + 26] ?? null);

            if (!$cedulaRif) {
                if (count($omitidos) < 500) {
                    $omitidos[] = [
                        'line'    => $lineNumber,
                        'cedula'  => '--',
                        'nombres' => $nombres,
                        'reason'  => 'cedula_rif vacía',
                    ];
                }
                continue;
            }

            $fechaParsed        = $this->parseDate($fecha);
            $fechaEstatusParsed = $this->parseDate($fechaEstatus);

            if ($estadoId === '0') $estadoId = null;
            if ($estadoId) $estadoId = (int) $estadoId;

            // Inferir banco desde prefijo de cuenta
            if ($ctaBancaria) {
                $prefijoBanco = ['0105' => 3, '0114' => 5, '0134' => 8];
                $prefijo = substr($ctaBancaria, 0, 4);
                if (isset($prefijoBanco[$prefijo])) {
                    $bancoId = $prefijoBanco[$prefijo];
                } elseif ($bancoId) {
                    $bancoId = (int) $bancoId;
                }
            } elseif ($bancoId) {
                $bancoId = (int) $bancoId;
            }

            // Detección de duplicados por cedula_rif
            if (isset($existingCedulas[$cedulaRif])) {
                $status     = 'update';
                $existingId = $existingCedulas[$cedulaRif];
            } else {
                $status     = 'new';
                $existingId = null;
            }

            $mapped = [
                'fecha'         => $fechaParsed,
                'letra'         => $letra,
                'cedula_rif'    => $cedulaRif,
                'nombres'       => $nombres,
                'apellidos'     => $apellidos,
                'email'         => $email,
                'email_alterno' => $emailAlt,
                'calle_avenida' => $calleAv,
                'piso_apto'     => $pisoApto,
                'edif_casa'     => $edifCasa,
                'urbanizacion'  => $urbanizacion,
                'ciudad'        => $ciudad,
                'estado_id'     => $estadoId,
                'telefono'      => $telefono,
                'fax'           => $fax,
                'celular'       => $celular,
                'otro'          => $otro,
                'banco_id'      => $bancoId,
                'cta_bancaria'  => $ctaBancaria,
                'tipo_cta'      => $tipoCta,
                'nom_usuario'   => $nomUsuario,
                'clave'         => $clave,
                'creado_por'    => $creadoPor,
                'cod_sucursal'  => $codSucursal,
                'estatus'       => $estatus,
                'fecha_estatus' => $fechaEstatusParsed,
                'observaciones' => $observaciones,
            ];

            $rows[] = [
                'line'                => $lineNumber,
                'status'              => $status,
                'errors'              => [],
                'existing_id'         => $existingId,
                'legacy_afilapto_id'  => $legacyAfilAptoId,
                'display'             => [
                    'cedula'    => ($letra ?? '') . '-' . $cedulaRif,
                    'nombres'   => $nombres,
                    'apellidos' => $apellidos,
                    'email'     => $email,
                    'estatus'   => $estatus,
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_afilpago_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        // Guardar omitidos para mostrarlos tras el execute
        $omitidosPath = storage_path('app/import_afilpago_omitidos_' . auth()->id() . '.json');
        file_put_contents($omitidosPath, json_encode($omitidos));

        $omitidosPorRazon = collect($omitidos)
            ->groupBy('reason')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->toArray();

        $summary = [
            'total'              => count($rows) + count($omitidos),
            'new'                => collect($rows)->where('status', 'new')->count(),
            'update'             => collect($rows)->where('status', 'update')->count(),
            'omitidos'           => count($omitidos),
            'omitidos_por_razon' => $omitidosPorRazon,
        ];

        return view('condominio.afilpagointegral-importar', compact('rows', 'summary', 'omitidos'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:update,skip',
        ]);

        $tempPath = storage_path('app/import_afilpago_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.afilpagointegral.importar')
                ->with('error', 'No hay datos para importar. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('condominio.afilpagointegral.importar')
                ->with('error', 'Sin filas validas.');
        }

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        // Mapa legacy_afilapto_id → afilpagointegral_id para vincular al importar afilapto
        $legacyMap = [];

        foreach ($rows as $row) {
            if ($row['status'] === 'error') {
                $results['errors'][] = [
                    'line'   => $row['line'],
                    'reason' => implode(', ', $row['errors']),
                    'ref'    => $row['display']['cedula'] ?? '',
                ];
                continue;
            }

            $data = array_filter($row['data'], fn($v) => $v !== null);
            $legacyAfilAptoId = $row['legacy_afilapto_id'] ?? null;

            try {
                $existing = Afilpagointegral::where('cedula_rif', $data['cedula_rif'] ?? '')->first();

                if ($existing) {
                    if ($duplicateAction === 'update') {
                        $existing->update($data);
                        $results['updated']++;
                        if ($legacyAfilAptoId) $legacyMap[$legacyAfilAptoId] = $existing->id;
                    } else {
                        $results['skipped']++;
                        if ($legacyAfilAptoId) $legacyMap[$legacyAfilAptoId] = $existing->id;
                    }
                } else {
                    $nuevo = Afilpagointegral::create($data);
                    $results['imported']++;
                    if ($legacyAfilAptoId) $legacyMap[$legacyAfilAptoId] = $nuevo->id;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'line'   => $row['line'],
                    'reason' => $e->getMessage(),
                    'ref'    => $row['display']['cedula'] ?? '',
                ];
            }
        }

        // Persistir el mapa para que el import de afilapto lo use
        $mapPath = storage_path('app/import_afilapto_legacy_map.json');
        file_put_contents($mapPath, json_encode($legacyMap));

        @unlink($tempPath);

        // Recuperar omitidos del preview
        $omitidosPath = storage_path('app/import_afilpago_omitidos_' . auth()->id() . '.json');
        $omitidos = [];
        if (file_exists($omitidosPath)) {
            $omitidos = json_decode(file_get_contents($omitidosPath), true) ?? [];
            @unlink($omitidosPath);
        }

        $omitidosPorRazon = collect($omitidos)
            ->groupBy('reason')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->toArray();

        return view('condominio.afilpagointegral-importar', compact('results', 'omitidos', 'omitidosPorRazon'));
    }

    private function clean(?string $value): ?string
    {
        if ($value === null || strtoupper(trim($value)) === 'NULL' || trim($value) === '') {
            return null;
        }
        return trim($value);
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
