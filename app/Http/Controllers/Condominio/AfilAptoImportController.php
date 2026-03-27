<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Compania;
use App\Models\Condominio\Edificio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AfilAptoImportController extends Controller
{
    public function showForm()
    {
        return view('condominio.afilapto-importar');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:51200',
        ]);

        $file = $request->file('archivo');
        $content = file_get_contents($file->getRealPath());
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $lines = explode("\n", $content);

        // Pre-load lookups (optional resolution, not required)
        $edificios = Edificio::pluck('id', 'cod_edif')->toArray();
        $companias = Compania::pluck('id', 'cod_compania')->toArray();
        $apartamentos = Apartamento::select('id', 'edificio_id', 'num_apto')
            ->get()
            ->mapWithKeys(fn($a) => [$a->edificio_id . '_' . $a->num_apto => $a->id])
            ->toArray();

        // Existing afilapto by legacy id for duplicate detection
        $existingByLegacy = Afilapto::pluck('id')->flip()->toArray();

        $rows = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $fields = str_getcsv($line, ',', '"');
            if (count($fields) < 6) continue;

            $lineNumber = $index + 1;
            $warnings = [];

            $legacyId    = $this->clean($fields[0] ?? null);
            $codAfil     = $this->clean($fields[1] ?? null);
            $codEdif     = $this->clean($fields[2] ?? null);
            $numApto     = $this->clean($fields[3] ?? null);
            $codComp     = $this->clean($fields[4] ?? null);
            $estatusAfil = $this->clean($fields[5] ?? null);
            $fechaAfil   = $this->clean($fields[6] ?? null);
            $obs         = $this->clean($fields[7] ?? null);

            // Validate legacy_id
            if (!$legacyId || !is_numeric($legacyId)) {
                $rows[] = $this->errorRow($lineNumber, ['ID legacy vacio o invalido'], $fields);
                continue;
            }

            // Resolve edificio_id (optional)
            $edificioId = null;
            if ($codEdif && isset($edificios[$codEdif])) {
                $edificioId = $edificios[$codEdif];
            } elseif ($codEdif) {
                $warnings[] = "Edificio '{$codEdif}' no encontrado";
            }

            // Resolve apartamento_id (optional)
            $apartamentoId = null;
            if ($edificioId && $numApto) {
                $key = $edificioId . '_' . $numApto;
                if (isset($apartamentos[$key])) {
                    $apartamentoId = $apartamentos[$key];
                } else {
                    $warnings[] = "Apto '{$numApto}' no encontrado en edif '{$codEdif}'";
                }
            }

            // Resolve compania_id (optional)
            $companiaId = null;
            if ($codComp && isset($companias[$codComp])) {
                $companiaId = $companias[$codComp];
            }

            // Parse date
            $fechaAfiliacion = null;
            if ($fechaAfil) {
                try {
                    $fechaAfiliacion = Carbon::parse($fechaAfil)->toDateTimeString();
                } catch (\Exception $e) {
                    $fechaAfiliacion = null;
                }
            }

            // Duplicate detection by legacy id
            $status = isset($existingByLegacy[(int)$legacyId]) ? 'update' : 'new';
            $existingId = $existingByLegacy[(int)$legacyId] ?? null;

            $mapped = [
                'legacy_id'       => (int) $legacyId,
                'apartamento_id'  => $apartamentoId,
                'edificio_id'     => $edificioId,
                'compania_id'     => $companiaId,
                'estatus_afil'    => $estatusAfil,
                'fecha_afiliacion' => $fechaAfiliacion,
                'observaciones'   => $obs,
            ];

            $rows[] = [
                'line'        => $lineNumber,
                'status'      => $status,
                'errors'      => [],
                'warnings'    => $warnings,
                'existing_id' => $existingId,
                'display'     => [
                    'legacy_id' => $legacyId,
                    'cod_afil'  => $codAfil,
                    'cod_edif'  => $codEdif,
                    'num_apto'  => $numApto,
                    'compania'  => $codComp,
                    'estatus'   => $estatusAfil,
                    'fecha'     => $fechaAfil,
                    'warnings'  => $warnings,
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_afilapto_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $totalWarnings = collect($rows)->sum(fn($r) => count($r['warnings'] ?? []));

        $summary = [
            'total'    => count($rows),
            'new'      => collect($rows)->where('status', 'new')->count(),
            'update'   => collect($rows)->where('status', 'update')->count(),
            'error'    => collect($rows)->where('status', 'error')->count(),
            'warnings' => $totalWarnings,
        ];

        return view('condominio.afilapto-importar', compact('rows', 'summary'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:update,skip',
        ]);

        $tempPath = storage_path('app/import_afilapto_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return redirect()->route('condominio.afilapto.importar')
                ->with('error', 'No hay datos para importar. Suba el archivo nuevamente.');
        }

        $rows = json_decode(file_get_contents($tempPath), true);
        if (empty($rows)) {
            @unlink($tempPath);
            return redirect()->route('condominio.afilapto.importar')
                ->with('error', 'Sin filas validas.');
        }

        // Disable FK triggers (apartamento_id/edificio_id may be null for unresolved refs)
        DB::statement('ALTER TABLE afilapto DISABLE TRIGGER ALL');

        $duplicateAction = $request->input('duplicate_action');
        $results = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        try {
            foreach ($rows as $row) {
                if ($row['status'] === 'error') {
                    $results['errors'][] = [
                        'line'   => $row['line'],
                        'reason' => implode(', ', $row['errors']),
                        'ref'    => ($row['display']['cod_edif'] ?? '') . '/' . ($row['display']['num_apto'] ?? ''),
                    ];
                    continue;
                }

                $data = $row['data'];
                $legacyId = $data['legacy_id'];
                unset($data['legacy_id']);
                // Keep nulls - they represent unresolved FKs
                $data = array_filter($data, fn($v) => $v !== null);

                try {
                    $existing = Afilapto::find($legacyId);

                    if ($existing) {
                        if ($duplicateAction === 'update') {
                            $existing->update($data);
                            $results['updated']++;
                        } else {
                            $results['skipped']++;
                        }
                    } else {
                        $record = new Afilapto($data);
                        $record->id = $legacyId;
                        $record->save();
                        $results['imported']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'line'   => $row['line'],
                        'reason' => $e->getMessage(),
                        'ref'    => ($row['display']['cod_edif'] ?? '') . '/' . ($row['display']['num_apto'] ?? ''),
                    ];
                }
            }
        } finally {
            DB::statement('ALTER TABLE afilapto ENABLE TRIGGER ALL');
        }

        // Fix PostgreSQL sequence
        try {
            $maxId = Afilapto::max('id') ?? 0;
            DB::statement("SELECT setval(pg_get_serial_sequence('afilapto', 'id'), GREATEST(?, 1), true)", [$maxId]);
        } catch (\Exception $e) {
            // Non-critical
        }

        @unlink($tempPath);
        return view('condominio.afilapto-importar', ['results' => $results]);
    }

    private function errorRow(int $line, array $errors, array $fields): array
    {
        return [
            'line'        => $line,
            'status'      => 'error',
            'errors'      => $errors,
            'warnings'    => [],
            'existing_id' => null,
            'display'     => [
                'legacy_id' => $fields[0] ?? '',
                'cod_afil'  => $fields[1] ?? '',
                'cod_edif'  => $fields[2] ?? '',
                'num_apto'  => $fields[3] ?? '',
                'compania'  => $fields[4] ?? '',
                'estatus'   => $fields[5] ?? '',
                'fecha'     => $fields[6] ?? '',
                'warnings'  => [],
            ],
            'data' => [],
        ];
    }

    private function clean(?string $value): ?string
    {
        if ($value === null || strtoupper(trim($value)) === 'NULL' || trim($value) === '') {
            return null;
        }
        return trim($value);
    }
}
