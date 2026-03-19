<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:sistema.gestionar-roles');
    }

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->paginate(15);

        return view('admin.roles', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return response()->json([
            'message'     => 'Formulario de creacion de rol',
            'module'      => 'Admin',
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|unique:roles,name',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rol creado exitosamente',
            'module'  => 'Admin',
            'data'    => $role->load('permissions'),
        ], 201);
    }

    public function show(Role $role)
    {
        return response()->json([
            'message' => 'Detalle del rol',
            'module'  => 'Admin',
            'data'    => $role->load('permissions'),
        ]);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return response()->json([
            'message'     => 'Formulario de edicion de rol',
            'module'      => 'Admin',
            'data'        => $role->load('permissions'),
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => 'sometimes|string|unique:roles,name,' . $role->id,
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if (isset($validated['name'])) {
            $role->update(['name' => $validated['name']]);
        }

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Rol actualizado exitosamente',
            'module'  => 'Admin',
            'data'    => $role->load('permissions'),
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado exitosamente',
            'module'  => 'Admin',
        ]);
    }

    public function attachPermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $role->givePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permiso asignado al rol exitosamente',
            'module'  => 'Admin',
            'data'    => $role->load('permissions'),
        ]);
    }

    public function detachPermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $role->revokePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permiso removido del rol exitosamente',
            'module'  => 'Admin',
            'data'    => $role->load('permissions'),
        ]);
    }
}
