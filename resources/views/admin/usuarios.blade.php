<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Usuarios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de usuarios del sistema</p>
            </div>
            <a href="{{ route('admin.usuarios.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear nuevo
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-cog mr-2 text-burgundy-800"></i>Listado de Usuarios
            </h3>
        </div>
        <div class="card-body p-0">
            @if($usuarios->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Cédula</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        @foreach($usuario->roles as $role)
                                            <span class="badge-info">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $usuario->cedula ?? 'N/A' }}</td>
                                    <td>
                                        @if($usuario->activo ?? true)
                                            <span class="badge-success">Activo</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.usuarios.show', $usuario) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
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
                    {{ $usuarios->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-cog text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay usuarios registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando el primer usuario del sistema.</p>
                    <a href="{{ route('admin.usuarios.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear usuario
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
