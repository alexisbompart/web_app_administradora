<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Roles</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de roles y permisos del sistema</p>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear nuevo
            </a>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-shield-alt mr-2 text-burgundy-800"></i>Listado de Roles
            </h3>
        </div>
        <div class="card-body p-0">
            @if($roles->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Guard</th>
                                <th>Permisos</th>
                                <th>Usuarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td>
                                        <span class="badge-info">{{ $role->permissions_count }} permisos</span>
                                    </td>
                                    <td>
                                        <span class="badge-warning">{{ $role->users_count }} usuarios</span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.roles.show', $role) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este rol?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $roles->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay roles registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando el primer rol del sistema.</p>
                    <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear rol
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
