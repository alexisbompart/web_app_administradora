<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de apartamentos del condominio</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.apartamentos.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('condominio.apartamentos.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Crear nuevo
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('condominio.apartamentos.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Num. Apto</label>
                    <input type="text" name="buscar" value="{{ $buscar ?? '' }}"
                           placeholder="Buscar número..."
                           class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Edificio</label>
                    <select name="edificio_id" class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                        <option value="">Todos</option>
                        @foreach($edificios as $edif)
                            <option value="{{ $edif->id }}" {{ $edificioId == $edif->id ? 'selected' : '' }}>
                                {{ $edif->cod_edif }} - {{ $edif->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Compañía</label>
                    <select name="compania_id" class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                        <option value="">Todas</option>
                        @foreach($companias as $comp)
                            <option value="{{ $comp->id }}" {{ $companiaId == $comp->id ? 'selected' : '' }}>
                                {{ $comp->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Estatus</label>
                    <select name="estatus" class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                        <option value="">Todos</option>
                        <option value="A" {{ $estatus === 'A' ? 'selected' : '' }}>Activo</option>
                        <option value="M" {{ $estatus === 'M' ? 'selected' : '' }}>Moroso</option>
                        <option value="I" {{ $estatus === 'I' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary text-sm px-4 py-1.5 flex-1">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                    @if($buscar || $edificioId || $companiaId || $estatus)
                        <a href="{{ route('condominio.apartamentos.index') }}" class="btn-secondary text-sm px-3 py-1.5" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-door-open mr-2 text-burgundy-800"></i>Listado de Apartamentos
                @if($buscar || $edificioId || $companiaId || $estatus)
                    <span class="ml-2 text-xs font-normal text-burgundy-800">(filtrado)</span>
                @endif
            </h3>
        </div>
        <div class="card-body p-0">
            @if($apartamentos->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Compañía</th>
                                <th>Edificio</th>
                                <th>Num Apto</th>
                                <th>Piso</th>
                                <th>Área m²</th>
                                <th>Alícuota</th>
                                <th>Propietario</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartamentos as $apartamento)
                                <tr>
                                    <td class="text-xs text-slate_custom-400">{{ $apartamento->edificio?->compania?->nombre ?? 'N/A' }}</td>
                                    <td>{{ $apartamento->edificio?->nombre ?? 'N/A' }}</td>
                                    <td class="font-medium text-navy-800">{{ $apartamento->num_apto }}</td>
                                    <td>{{ $apartamento->piso }}</td>
                                    <td>{{ $apartamento->area_m2 }}</td>
                                    <td>{{ $apartamento->alicuota }}%</td>
                                    <td>{{ $apartamento->propietario }}</td>
                                    <td>
                                        @if($apartamento->estatus == 'activo' || $apartamento->estatus == 'A')
                                            <span class="badge-success">Activo</span>
                                        @elseif($apartamento->estatus == 'moroso' || $apartamento->estatus == 'M')
                                            <span class="badge-warning">Moroso</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('condominio.apartamentos.show', $apartamento) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('condominio.apartamentos.edit', $apartamento) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('condominio.apartamentos.destroy', $apartamento) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este apartamento?')">
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
                    {{ $apartamentos->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-door-open text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay apartamentos registrados</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comienza creando el primer apartamento del condominio.</p>
                    <a href="{{ route('condominio.apartamentos.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear apartamento
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
