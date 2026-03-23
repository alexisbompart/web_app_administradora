<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Rol</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $role->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Info del Rol --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-shield-alt mr-2 text-burgundy-800"></i>Informacion del Rol
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $role->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Guard</p>
                    <p class="text-sm font-medium text-navy-800">{{ $role->guard_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Permisos</p>
                    <span class="badge-info">{{ $role->permissions->count() }} permisos</span>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Usuarios</p>
                    <span class="badge-warning">{{ $role->users->count() }} usuarios</span>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Fecha de Creacion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $role->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Ultima Actualizacion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $role->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Permisos Asignados --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-key mr-2 text-burgundy-800"></i>Permisos Asignados
            </h3>
        </div>
        <div class="card-body">
            @if($role->permissions->count())
                @php
                    $grouped = $role->permissions->groupBy(function ($p) {
                        return explode('.', $p->name)[0] ?? 'general';
                    });
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($grouped as $group => $perms)
                    <div class="border border-slate_custom-200 rounded-xl overflow-hidden">
                        <div class="bg-slate_custom-50 px-4 py-2.5 border-b border-slate_custom-200">
                            <span class="text-sm font-heading font-bold text-navy-800 capitalize">{{ str_replace('-', ' ', $group) }}</span>
                            <span class="text-xs text-slate_custom-400 ml-1">({{ $perms->count() }})</span>
                        </div>
                        <div class="p-3 space-y-1">
                            @foreach($perms as $permission)
                            <div class="flex items-center gap-2 p-2">
                                <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                <span class="text-sm text-navy-800">{{ $permission->name }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate_custom-400 text-center py-4">Este rol no tiene permisos asignados.</p>
            @endif
        </div>
    </div>

    {{-- Usuarios con este Rol --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-users mr-2 text-burgundy-800"></i>Usuarios con este Rol
            </h3>
        </div>
        <div class="card-body p-0">
            @if($role->users->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                            <tr>
                                <td class="font-medium text-navy-800">{{ $user->name }}</td>
                                <td class="text-slate_custom-500">{{ $user->email }}</td>
                                <td>
                                    @if($user->activo)
                                        <span class="badge-success">Activo</span>
                                    @else
                                        <span class="badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.usuarios.show', $user) }}" class="btn-secondary text-xs px-2 py-1">
                                        <i class="fas fa-eye mr-1"></i>Ver
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <p class="text-sm text-slate_custom-400">No hay usuarios con este rol asignado.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
