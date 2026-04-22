<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Afiliaciones de Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestion de afiliaciones por apartamento (afilapto)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('condominio.afiliaciones-apto.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Crear nuevo
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Alertas de registros incompletos --}}
    @if($sinApto > 0 || $sinEdificio > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
        @if($sinApto > 0)
        <a href="{{ route('condominio.afiliaciones-apto.index', ['filtro' => 'sin_apto']) }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl border {{ $filtro === 'sin_apto' ? 'bg-amber-100 border-amber-400' : 'bg-amber-50 border-amber-200 hover:bg-amber-100' }} transition">
            <div class="w-9 h-9 bg-amber-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-door-closed text-amber-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-amber-800">{{ number_format($sinApto) }} sin apartamento vinculado</p>
                <p class="text-xs text-amber-600">El campo apartamento_id está vacío — no se puede asociar a un apto</p>
            </div>
        </a>
        @endif
        @if($sinEdificio > 0)
        <a href="{{ route('condominio.afiliaciones-apto.index', ['filtro' => 'sin_edificio']) }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl border {{ $filtro === 'sin_edificio' ? 'bg-red-100 border-red-400' : 'bg-red-50 border-red-200 hover:bg-red-100' }} transition">
            <div class="w-9 h-9 bg-red-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-building text-red-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-800">{{ number_format($sinEdificio) }} sin edificio vinculado</p>
                <p class="text-xs text-red-600">El campo edificio_id está vacío — no se puede asociar a un edificio</p>
            </div>
        </a>
        @endif
    </div>
    @endif

    {{-- Filtros de búsqueda --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('condominio.afiliaciones-apto.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Edificio</label>
                    <select name="edificio_id" class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                        <option value="">Todos</option>
                        @foreach($edificios as $edif)
                            <option value="{{ $edif->id }}" {{ $edificioId == $edif->id ? 'selected' : '' }}>
                                {{ $edif->cod_edif }} - {{ \Illuminate\Support\Str::limit($edif->nombre, 25) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Num. Apto</label>
                    <input type="text" name="apto" value="{{ $buscarApto ?? '' }}"
                           placeholder="Ej: 3-A, 101..."
                           class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Cod. PINT</label>
                    <input type="text" name="pint" value="{{ $buscarPint ?? '' }}"
                           placeholder="Ej: 01029122..."
                           class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate_custom-500 mb-1">Estatus</label>
                    <select name="estatus" class="w-full text-sm border border-slate_custom-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-burgundy-800/30">
                        <option value="">Todos</option>
                        <option value="A" {{ $estatus === 'A' ? 'selected' : '' }}>Activo</option>
                        <option value="P" {{ $estatus === 'P' ? 'selected' : '' }}>Pendiente</option>
                        <option value="D" {{ $estatus === 'D' ? 'selected' : '' }}>Desactivado</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary text-sm px-4 py-1.5 flex-1">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                    @if($edificioId || $buscarApto || $buscarPint || $estatus)
                        <a href="{{ route('condominio.afiliaciones-apto.index', $filtro ? ['filtro' => $filtro] : []) }}"
                           class="btn-secondary text-sm px-3 py-1.5" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-link mr-2 text-burgundy-800"></i>Listado de Afiliaciones ({{ $afilaptos->total() }})
                    @if($filtro || $edificioId || $buscarApto || $buscarPint || $estatus)
                        <span class="ml-2 text-xs font-normal text-burgundy-800">(filtrado)</span>
                    @endif
                </h3>
                @if($filtro)
                <a href="{{ route('condominio.afiliaciones-apto.index') }}" class="btn-secondary text-xs px-3 py-1">
                    <i class="fas fa-times mr-1"></i>Quitar filtro
                </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($afilaptos->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>PINT</th>
                                <th>Edificio</th>
                                <th>Apartamento</th>
                                <th>Compania</th>
                                <th>Estatus</th>
                                <th>Fecha Afil.</th>
                                <th>Pago Integral</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($afilaptos as $afil)
                            <tr>
                                <td class="text-xs text-slate_custom-400">{{ $afil->id }}</td>
                                <td class="font-medium text-navy-800 text-xs">{{ $afil->cod_pint ?? '--' }}</td>
                                <td>
                                    @if($afil->edificio)
                                        <span class="text-sm">{{ $afil->edificio->nombre ?? $afil->edificio->cod_edif }}</span>
                                    @elseif($afil->edificio_id)
                                        <span class="text-xs text-red-500" title="edificio_id={{ $afil->edificio_id }}">
                                            <i class="fas fa-exclamation-circle mr-1"></i>ID {{ $afil->edificio_id }} no existe
                                        </span>
                                    @else
                                        <span class="text-xs text-amber-500">
                                            <i class="fas fa-unlink mr-1"></i>Sin edificio
                                        </span>
                                    @endif
                                </td>
                                <td class="font-medium text-navy-800">
                                    @if($afil->apartamento)
                                        {{ $afil->apartamento->num_apto }}
                                    @elseif($afil->apartamento_id)
                                        <span class="text-xs text-red-500" title="apartamento_id={{ $afil->apartamento_id }}">
                                            <i class="fas fa-exclamation-circle mr-1"></i>ID {{ $afil->apartamento_id }} no existe
                                        </span>
                                    @else
                                        <span class="text-xs text-amber-500">
                                            <i class="fas fa-unlink mr-1"></i>Sin apto vinculado
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs">{{ $afil->compania?->nombre ?? '--' }}</td>
                                <td>
                                    @if($afil->estatus_afil === 'A')<span class="badge-success text-xs">Activo</span>
                                    @elseif($afil->estatus_afil === 'D')<span class="badge-danger text-xs">Desact.</span>
                                    @else<span class="badge-info text-xs">{{ $afil->estatus_afil ?? '--' }}</span>@endif
                                </td>
                                <td class="text-xs">{{ $afil->fecha_afiliacion?->format('d/m/Y') ?? '--' }}</td>
                                <td>
                                    @if($afil->afilpagointegral)
                                        <span class="badge-success text-xs"><i class="fas fa-check mr-1"></i>Si</span>
                                    @else
                                        <span class="badge-warning text-xs">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('condominio.afiliaciones-apto.edit', $afil) }}" class="btn-secondary text-xs px-2 py-1" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('condominio.afiliaciones-apto.destroy', $afil) }}" method="POST" onsubmit="return confirm('Eliminar esta afiliacion y su pago integral asociado?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $afilaptos->links() }}</div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-link text-2xl text-slate_custom-400"></i></div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay afiliaciones registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Importe el archivo afilapto.csv o cree una manualmente.</p>
                    <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-primary"><i class="fas fa-file-import mr-2"></i>Importar</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
