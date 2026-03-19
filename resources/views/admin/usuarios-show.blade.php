<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Usuario</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $usuario->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-cog mr-2 text-burgundy-800"></i>Informacion del Usuario
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Cedula</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->cedula ?? 'No registrada' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Telefono</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->telefono ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Rol</p>
                    <div class="flex flex-wrap gap-1">
                        @forelse($usuario->roles as $role)
                            <span class="badge-info">{{ $role->name }}</span>
                        @empty
                            <span class="text-sm text-slate_custom-400">Sin rol asignado</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estatus</p>
                    @if($usuario->activo)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-danger">Inactivo</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Fecha de Registro</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Ultima Actualizacion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $usuario->updated_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Permisos del Usuario --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-key mr-2 text-burgundy-800"></i>Permisos Asignados
            </h3>
        </div>
        <div class="card-body">
            @if($usuario->getAllPermissions()->count())
                <div class="flex flex-wrap gap-2">
                    @foreach($usuario->getAllPermissions() as $permission)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate_custom-100 text-navy-800">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>{{ $permission->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate_custom-400 text-center py-4">Este usuario no tiene permisos asignados.</p>
            @endif
        </div>
    </div>
</x-app-layout>
