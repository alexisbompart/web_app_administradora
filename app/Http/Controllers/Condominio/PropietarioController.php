<?php

namespace App\Http\Controllers\Condominio;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Afilpagointegral;
use App\Models\Condominio\Propietario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PropietarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.ver-dashboard');
    }

    public function index()
    {
        $propietarios = Propietario::with(['apartamentos.edificio'])->paginate(15);

        return view('condominio.propietarios', compact('propietarios'));
    }

    public function create()
    {
        return view('condominio.propietarios-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:propietarios,cedula',
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'estatus'   => 'boolean',
        ]);

        Propietario::create($request->all());

        return redirect()->route('condominio.propietarios.index')
            ->with('success', 'Registro creado exitosamente');
    }

    public function show(Propietario $propietario)
    {
        $propietario->load(['apartamentos.edificio', 'apartamentos.condDeudasApto']);

        return view('condominio.propietarios-show', compact('propietario'));
    }

    public function edit(Propietario $propietario)
    {
        return view('condominio.propietarios-form', compact('propietario'));
    }

    public function update(Request $request, Propietario $propietario)
    {
        $request->validate([
            'cedula'    => 'required|string|max:20|unique:propietarios,cedula,' . $propietario->id,
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'estatus'   => 'boolean',
        ]);

        $propietario->update($request->all());

        return redirect()->route('condominio.propietarios.show', $propietario)
            ->with('success', 'Registro actualizado exitosamente');
    }

    public function destroy(Propietario $propietario)
    {
        $propietario->delete();

        return redirect()->route('condominio.propietarios.index')
            ->with('success', 'Registro eliminado exitosamente');
    }

    public function previewGenerate()
    {
        $afiliados = Afilpagointegral::with('afilapto')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('cedula_rif')
            ->where('cedula_rif', '!=', '')
            ->get();

        $existingCedulas = Propietario::pluck('cedula')->toArray();
        $existingEmails = User::pluck('email')->toArray();

        $toCreate = [];
        $skipped = [];
        $seenCedulas = [];
        $seenEmails = [];

        foreach ($afiliados as $afil) {
            $cedula = trim($afil->cedula_rif);
            $email = trim(strtolower($afil->email));

            if (in_array($cedula, $existingCedulas) || isset($seenCedulas[$cedula])) {
                $skipped[] = ['cedula' => $cedula, 'nombre' => $afil->nombres . ' ' . $afil->apellidos, 'razon' => 'Cedula ya existe'];
                continue;
            }
            if (in_array($email, $existingEmails) || isset($seenEmails[$email])) {
                $skipped[] = ['cedula' => $cedula, 'nombre' => $afil->nombres . ' ' . $afil->apellidos, 'razon' => 'Email ya existe'];
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = ['cedula' => $cedula, 'nombre' => $afil->nombres . ' ' . $afil->apellidos, 'razon' => 'Email invalido'];
                continue;
            }

            $seenCedulas[$cedula] = true;
            $seenEmails[$email] = true;

            $toCreate[] = [
                'cedula' => $cedula,
                'nombres' => mb_convert_case(trim($afil->nombres), MB_CASE_TITLE),
                'apellidos' => mb_convert_case(trim($afil->apellidos), MB_CASE_TITLE),
                'email' => $email,
                'telefono' => $afil->telefono,
                'celular' => $afil->celular,
                'apartamento_id' => $afil->afilapto?->apartamento_id,
                'fecha_afiliacion' => $afil->afilapto?->fecha_afiliacion?->toDateString(),
            ];
        }

        // Guardar en archivo temporal para procesar por lotes
        $tempPath = storage_path('app/generate_propietarios_' . auth()->id() . '.json');
        file_put_contents($tempPath, json_encode($toCreate));

        return view('condominio.propietarios-generate', compact('toCreate', 'skipped'));
    }

    public function executeBatch(Request $request)
    {
        $tempPath = storage_path('app/generate_propietarios_' . auth()->id() . '.json');
        if (!file_exists($tempPath)) {
            return response()->json(['error' => 'No hay datos preparados. Ejecute la vista previa primero.'], 400);
        }

        $allRows = json_decode(file_get_contents($tempPath), true);
        if (empty($allRows)) {
            @unlink($tempPath);
            return response()->json(['error' => 'Sin registros para procesar.'], 400);
        }

        $offset = (int) $request->input('offset', 0);
        $batchSize = 50;
        $total = count($allRows);
        $batch = array_slice($allRows, $offset, $batchSize);

        $created = 0;
        $skipped = 0;
        $errors = [];

        $existingCedulas = Propietario::pluck('cedula')->toArray();
        $existingEmails = User::pluck('email')->toArray();

        foreach ($batch as $row) {
            if (in_array($row['cedula'], $existingCedulas) || in_array($row['email'], $existingEmails)) {
                $skipped++;
                continue;
            }

            try {
                $user = User::create([
                    'name' => $row['nombres'] . ' ' . $row['apellidos'],
                    'email' => $row['email'],
                    'password' => Hash::make($row['cedula']),
                    'cedula' => $row['cedula'],
                    'telefono' => $row['telefono'],
                    'activo' => true,
                ]);

                $user->assignRole('cliente-propietario');

                $propietario = Propietario::create([
                    'user_id' => $user->id,
                    'cedula' => $row['cedula'],
                    'nombres' => $row['nombres'],
                    'apellidos' => $row['apellidos'],
                    'email' => $row['email'],
                    'telefono' => $row['telefono'],
                    'celular' => $row['celular'],
                    'estatus' => true,
                ]);

                if (!empty($row['apartamento_id'])) {
                    $propietario->apartamentos()->attach($row['apartamento_id'], [
                        'propietario_actual' => true,
                        'fecha_desde' => $row['fecha_afiliacion'] ?? now()->toDateString(),
                    ]);
                }

                $existingCedulas[] = $row['cedula'];
                $existingEmails[] = $row['email'];
                $created++;
            } catch (\Exception $e) {
                $errors[] = ['cedula' => $row['cedula'], 'reason' => $e->getMessage()];
            }
        }

        $nextOffset = $offset + $batchSize;
        $finished = $nextOffset >= $total;

        if ($finished) {
            @unlink($tempPath);
        }

        return response()->json([
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
            'processed' => min($nextOffset, $total),
            'total' => $total,
            'finished' => $finished,
            'percent' => min(100, round(($nextOffset / max($total, 1)) * 100)),
            'current_name' => !empty($batch) ? ($batch[0]['nombres'] . ' ' . $batch[0]['apellidos']) : '',
        ]);
    }
}
