<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Condominio\Apartamento;
use App\Models\Condominio\Edificio;
use App\Models\Condominio\Propietario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.gestionar-usuarios');
    }

    public function index()
    {
        $usuarios = User::with('roles')->paginate(15);

        return view('admin.usuarios', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::all();
        $edificios = Edificio::orderBy('nombre')->get();

        return view('admin.usuarios-form', compact('roles', 'edificios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
            'role'           => 'required|exists:roles,name',
            'cedula'         => 'nullable|string|max:20',
            'telefono'       => 'nullable|string|max:20',
            'activo'         => 'nullable|boolean',
            'apartamento_id' => 'nullable|exists:cond_aptos,id',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
                'cedula'   => $validated['cedula'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'activo'   => $validated['activo'] ?? true,
            ]);

            $user->assignRole($validated['role']);

            if ($validated['role'] === 'cliente-propietario') {
                $propietario = $this->crearPropietario($user);

                if (!empty($validated['apartamento_id'])) {
                    $propietario->apartamentos()->attach($validated['apartamento_id'], [
                        'propietario_actual' => true,
                        'fecha_desde'        => now(),
                    ]);
                }
            }

            return $user;
        });

        $msg = 'Usuario creado exitosamente.';
        if ($validated['role'] === 'cliente-propietario') {
            $msg .= ' Se creo automaticamente el perfil de propietario.';
            if (!empty($validated['apartamento_id'])) {
                $msg .= ' Apartamento asignado.';
            }
        }

        return redirect()->route('admin.usuarios.index')
            ->with('success', $msg);
    }

    public function show(User $usuario)
    {
        $usuario->load('roles', 'permissions');

        return view('admin.usuarios-show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $usuario->load('roles');
        $roles = Role::all();
        $edificios = Edificio::orderBy('nombre')->get();

        // Load current apartment if propietario exists
        $propietario = $usuario->propietario;
        $apartamentoActual = null;
        if ($propietario) {
            $apartamentoActual = $propietario->apartamentos()
                ->wherePivot('propietario_actual', true)->first();
        }

        return view('admin.usuarios-form', compact('usuario', 'roles', 'edificios', 'propietario', 'apartamentoActual'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:users,email,' . $usuario->id,
            'password'       => 'nullable|string|min:8|confirmed',
            'role'           => 'sometimes|exists:roles,name',
            'cedula'         => 'nullable|string|max:20',
            'telefono'       => 'nullable|string|max:20',
            'activo'         => 'nullable|boolean',
            'apartamento_id' => 'nullable|exists:cond_aptos,id',
        ]);

        $dataToUpdate = collect($validated)->except(['password', 'role', 'apartamento_id'])->toArray();

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = bcrypt($validated['password']);
        }

        DB::transaction(function () use ($usuario, $dataToUpdate, $validated, $request) {
            $usuario->update($dataToUpdate);

            if ($request->has('role')) {
                $oldRole = $usuario->roles->first()?->name;
                $newRole = $validated['role'];
                $usuario->syncRoles([$newRole]);

                // Auto-create propietario if switching TO cliente-propietario and none exists
                if ($newRole === 'cliente-propietario' && $oldRole !== 'cliente-propietario') {
                    if (!$usuario->propietario) {
                        $propietario = $this->crearPropietario($usuario);
                    }
                }
            }

            // Assign apartment if provided and user is propietario
            if (!empty($validated['apartamento_id']) && $usuario->propietario) {
                $prop = $usuario->propietario;
                $currentApto = $prop->apartamentos()->wherePivot('propietario_actual', true)->first();
                if (!$currentApto || $currentApto->id != $validated['apartamento_id']) {
                    // Mark old as not current
                    if ($currentApto) {
                        $prop->apartamentos()->updateExistingPivot($currentApto->id, [
                            'propietario_actual' => false,
                            'fecha_hasta'        => now(),
                        ]);
                    }
                    // Attach new
                    $prop->apartamentos()->attach($validated['apartamento_id'], [
                        'propietario_actual' => true,
                        'fecha_desde'        => now(),
                    ]);
                }
            }
        });

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * API: apartamentos de un edificio (para select dinamico).
     */
    public function apartamentosPorEdificio(Edificio $edificio)
    {
        return Apartamento::where('edificio_id', $edificio->id)
            ->orderBy('num_apto')
            ->get(['id', 'num_apto']);
    }

    /**
     * Crea un registro de Propietario vinculado al usuario.
     */
    private function crearPropietario(User $user): Propietario
    {
        $parts = explode(' ', $user->name, 2);

        return Propietario::create([
            'user_id'   => $user->id,
            'cedula'    => $user->cedula,
            'nombres'   => $parts[0],
            'apellidos' => $parts[1] ?? '',
            'email'     => $user->email,
            'telefono'  => $user->telefono,
            'estatus'   => true,
        ]);
    }
}
