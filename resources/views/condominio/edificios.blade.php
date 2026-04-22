<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Edificios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de edificios del condominio</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.edificios.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('condominio.edificios.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Crear nuevo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-city mr-2 text-burgundy-800"></i>Listado de Edificios
                </h3>
                <form method="GET" action="{{ route('condominio.edificios.index') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <input type="text" name="buscar" value="{{ $buscar ?? '' }}"
                               placeholder="Buscar por nombre o código..."
                               class="pl-9 pr-4 py-1.5 text-sm border border-slate_custom-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-burgundy-800/30 w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate_custom-400 text-xs"></i>
                    </div>
                    <button type="submit" class="btn-primary text-xs px-3 py-1.5">Buscar</button>
                    @if($buscar)
                        <a href="{{ route('condominio.edificios.index') }}" class="btn-secondary text-xs px-3 py-1.5">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($edificios->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Compañía</th>
                                <th>Total Aptos</th>
                                <th>Ciudad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($edificios as $edificio)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $edificio->cod_edif }}</td>
                                    <td>{{ $edificio->nombre }}</td>
                                    <td>{{ $edificio->compania?->nombre ?? 'N/A' }}</td>
                                    <td>{{ $edificio->total_aptos }}</td>
                                    <td>{{ $edificio->ciudad }}</td>
                                    <td>
                                        @if($edificio->activo)
                                            <span class="badge-success">Activo</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('condominio.edificios.show', $edificio) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('condominio.edificios.edit', $edificio) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('condominio.edificios.destroy', $edificio) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este edificio?')">
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
                    {{ $edificios->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-city text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay edificios registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando el primer edificio del condominio.</p>
                    <a href="{{ route('condominio.edificios.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear edificio
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
