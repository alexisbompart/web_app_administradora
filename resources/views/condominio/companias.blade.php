<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Companias</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de companias del sistema</p>
            </div>
            <a href="{{ route('condominio.companias.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear nueva
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>Listado de Companias
            </h3>
        </div>
        <div class="card-body p-0">
            @if($companias->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>RIF</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companias as $compania)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $compania->cod_compania }}</td>
                                    <td>{{ $compania->nombre }}</td>
                                    <td>{{ $compania->rif }}</td>
                                    <td>{{ $compania->telefono }}</td>
                                    <td>{{ $compania->email }}</td>
                                    <td>
                                        @if($compania->activo)
                                            <span class="badge-success">Activo</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('condominio.companias.show', $compania) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('condominio.companias.edit', $compania) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('condominio.companias.destroy', $compania) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta compania?')">
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
                    {{ $companias->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay companias registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando la primera compania del sistema.</p>
                    <a href="{{ route('condominio.companias.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear compania
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
