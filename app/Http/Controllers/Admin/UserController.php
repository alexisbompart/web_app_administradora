<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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

        return view('admin.usuarios-form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
            'cedula'   => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo'   => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'cedula'   => $validated['cedula'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'activo'   => $validated['activo'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
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

        return view('admin.usuarios-form', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'sometimes|exists:roles,name',
            'cedula'   => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'activo'   => 'nullable|boolean',
        ]);

        $dataToUpdate = collect($validated)->except(['password', 'role'])->toArray();

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = bcrypt($validated['password']);
        }

        $usuario->update($dataToUpdate);

        if ($request->has('role')) {
            $usuario->syncRoles([$validated['role']]);
        }

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
