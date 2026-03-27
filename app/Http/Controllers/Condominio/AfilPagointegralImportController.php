<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilapto;
use App\Models\Condominio\Afilpagointegral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $file = $request->file('archivo');
        $content = file_get_contents($file->getRealPath());
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $lines = explode("\n", $content);

        // Pre-load existing afilpagointegral for duplicate detection
        $existingPago = Afilpagointegral::pluck('id', 'afilapto_id')->toArray();

        $rows = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $fields = str_getcsv($line, ',', '"');
            if (count($fields) < 28) continue;

            $lineNumber = $index + 1;
            $errors = [];

            $afilAptoId   = $this->clean($fields[0] ?? null);
            $fecha        = $this->clean($fields[1] ?? null);
            $letra        = $this->clean($fields[2] ?? null);
            $cedulaRif    = $this->clean($fields[3] ?? null);
            $nombres      = $this->clean($fields[4] ?? null);
            $apellidos    = $this->clean($fields[5] ?? null);
            $email        = $this->clean($fields[6] ?? null);
            $emailAlt     = $this->clean($fields[7] ?? null);
            $calleAv      = $this->clean($fields[8] ?? null);
            $pisoApto     = $this->clean($fields[9] ?? null);
            $edifCasa     = $this->clean($fields[10] ?? null);
            $urbanizacion = $this->clean($fields[11] ?? null);
            $ciudad       = $this->clean($fields[12] ?? null);
            $estadoId     = $this->clean($fields[13] ?? null);
            $telefono     = $this->clean($fields[14] ?? null);
            $fax          = $this->clean($fields[15] ?? null);
            $celular      = $this->clean($fields[16] ?? null);
            $otro         = $this->clean($fields[17] ?? null);
            $bancoId      = $this->clean($fields[18] ?? null);
            $ctaBancaria  = $this->clean($fields[19] ?? null);
            $tipoCta      = $this->clean($fields[20] ?? null);
            $nomUsuario   = $this->clean($fields[21] ?? null);
            $clave        = $this->clean($fields[22] ?? null);
            $creadoPor    = $this->clean($fields[23] ?? null);
            $codSucursal  = $this->clean($fields[24] ?? null);
            $estatus      = $this->clean($fields[25] ?? null);
            $fechaEstatus = $this->clean($fields[26] ?? null);
            $observaciones = $this->clean($fields[27] ?? null);

            // Validate afilapto_id (solo que sea numerico, la FK se resuelve al importar afilapto despues)
            $afilId = null;
            if ($afilAptoId && is_numeric($afilAptoId)) {
                $afilId = (int) $afilAptoId;
            } else {
                $errors[] = "afilapto_id vacio o invalido";
            }

            // Parse dates
            $fechaParsed = $this->parseDate($fecha);
            $fechaEstatusParsed = $this->parseDate($fechaEstatus);

            // Fix estado_id: treat "0" as null
            if ($estadoId === '0') $estadoId = null;
            if ($estadoId) $estadoId = (int) $estadoId;

            // Fix banco_id: asignar banco correcto segun prefijo de cuenta
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

            // Duplicate detection
            $status = 'error';
            $existingId = null;
            if (empty($errors) && $afilId) {
                if (isset($existingPago[$afilId])) {
                    $status = 'update';
                    $existingId = $existingPago[$afilId];
                } else {
                    $status = 'new';
                }
            }

            $mapped = [
                'afilapto_id' => $afilId,
                'fecha' => $fechaParsed,
                'letra' => $letra,
                'cedula_rif' => $cedulaRif,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'email' => $email,
                'email_alterno' => $emailAlt,
                'calle_avenida' => $calleAv,
                'piso_apto' => $pisoApto,
                'edif_casa' => $edifCasa,
                'urbanizacion' => $urbanizacion,
                'ciudad' => $ciudad,
                'estado_id' => $estadoId,
                'telefono' => $telefono,
                'fax' => $fax,
                'celular' => $celular,
                'otro' => $otro,
                'banco_id' => $bancoId,
                'cta_bancaria' => $ctaBancaria,
                'tipo_cta' => $tipoCta,
                'nom_usuario' => $nomUsuario,
                'clave' => $clave,
                'creado_por' => $creadoPor,
                'cod_sucursal' => $codSucursal,
                'estatus' => $estatus,
                'fecha_estatus' => $fechaEstatusParsed,
                'observaciones' => $observaciones,
            ];

            $rows[] = [
                'line' => $lineNumber,
                'status' => $status,
                'errors' => $errors,
                'existing_id' => $existingId,
                'display' => [
                    'afilapto_id' => $afilAptoId,
                    'cedula' => ($letra ?? '') . '-' . ($cedulaRif ?? ''),
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'estatus' => $estatus,
                ],
                'data' => $mapped,
            ];
        }

        $tempPath = storage_path('app/import_afilpago_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($rows));

        $summary = [
            'total' => count($rows),
            'new' => collect($rows)->where('status', 'new')->count(),
            'update' => collect($rows)->where('status', 'update')->count(),
            'error' => collect($rows)->where('status', 'error')->count(),
        ];

        return view('condominio.afilpagointegral-importar', compact('rows', 'summary'));
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

        // Desactivar FK constraints temporalmente (afilapto se importa despues)
        DB::statement('ALTER TABLE afilpagointegral DISABLE TRIGGER ALL');

        try {
            foreach ($rows as $row) {
                if ($row['status'] === 'error') {
                    $results['errors'][] = [
                        'line' => $row['line'],
                        'reason' => implode(', ', $row['errors']),
                        'ref' => $row['display']['cedula'] ?? '',
                    ];
                    continue;
                }

                $data = array_filter($row['data'], fn($v) => $v !== null);

                try {
                    $existing = Afilpagointegral::where('afilapto_id', $data['afilapto_id'] ?? 0)->first();

                    if ($existing) {
                        if ($duplicateAction === 'update') {
                            $existing->update($data);
                            $results['updated']++;
                        } else {
                            $results['skipped']++;
                        }
                    } else {
                        Afilpagointegral::create($data);
                        $results['imported']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'line' => $row['line'],
                        'reason' => $e->getMessage(),
                        'ref' => $row['display']['cedula'] ?? '',
                    ];
                }
            }
        } finally {
            // Siempre reactivar triggers, incluso si hay error
            DB::statement('ALTER TABLE afilpagointegral ENABLE TRIGGER ALL');
        }

        @unlink($tempPath);
        return view('condominio.afilpagointegral-importar', ['results' => $results]);
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
