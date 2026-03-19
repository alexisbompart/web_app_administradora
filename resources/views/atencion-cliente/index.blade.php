<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Atencion al Cliente</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestion de solicitudes y reclamos</p>
            </div>
            <a href="{{ route('servicios.atencion-cliente.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nueva Solicitud
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.atencion-cliente.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Tipo</label>
                    <select name="tipo" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todos</option>
                        <option value="consulta" {{ request('tipo') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                        <option value="queja" {{ request('tipo') == 'queja' ? 'selected' : '' }}>Queja</option>
                        <option value="solicitud" {{ request('tipo') == 'solicitud' ? 'selected' : '' }}>Solicitud</option>
                        <option value="emergencia" {{ request('tipo') == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Estatus</label>
                    <select name="estatus" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todos</option>
                        <option value="abierto" {{ request('estatus') == 'abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="en_proceso" {{ request('estatus') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="resuelto" {{ request('estatus') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        <option value="cerrado" {{ request('estatus') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Prioridad</label>
                    <select name="prioridad" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todas</option>
                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="urgente" {{ request('prioridad') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-headset mr-2 text-burgundy-800"></i>Solicitudes
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Asunto</th>
                            <th>Edificio</th>
                            <th>Prioridad</th>
                            <th>Estatus</th>
                            <th>Fecha Apertura</th>
                            <th>Ejecutivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>
                                @switch($solicitud->tipo)
                                    @case('queja')
                                        <span class="inline-flex items-center gap-1 text-red-600"><i class="fas fa-exclamation-circle"></i> Queja</span>
                                        @break
                                    @case('consulta')
                                        <span class="inline-flex items-center gap-1 text-blue-600"><i class="fas fa-question-circle"></i> Consulta</span>
                                        @break
                                    @case('solicitud')
                                        <span class="inline-flex items-center gap-1 text-green-600"><i class="fas fa-clipboard-list"></i> Solicitud</span>
                                        @break
                                    @case('emergencia')
                                        <span class="inline-flex items-center gap-1 text-red-700 font-bold"><i class="fas fa-bolt"></i> Emergencia</span>
                                        @break
                                    @case('asesoria_legal')
                                        <span class="inline-flex items-center gap-1 text-purple-600"><i class="fas fa-gavel"></i> Asesoria Legal</span>
                                        @break
                                    @case('asamblea')
                                        <span class="inline-flex items-center gap-1 text-indigo-600"><i class="fas fa-users"></i> Asamblea</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center gap-1 text-slate_custom-500"><i class="fas fa-tag"></i> {{ ucfirst($solicitud->tipo) }}</span>
                                @endswitch
                            </td>
                            <td class="font-medium">{{ Str::limit($solicitud->asunto, 40) }}</td>
                            <td>{{ $solicitud->edificio->nombre ?? 'N/A' }}</td>
                            <td>
                                @switch($solicitud->prioridad)
                                    @case('urgente')
                                        <span class="badge-danger">Urgente</span>
                                        @break
                                    @case('alta')
                                        <span class="badge-danger">Alta</span>
                                        @break
                                    @case('media')
                                        <span class="badge-warning">Media</span>
                                        @break
                                    @case('baja')
                                        <span class="badge-info">Baja</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($solicitud->estatus)
                                    @case('abierto')
                                        <span class="badge-warning">Abierto</span>
                                        @break
                                    @case('en_proceso')
                                        <span class="badge-info">En Proceso</span>
                                        @break
                                    @case('resuelto')
                                        <span class="badge-success">Resuelto</span>
                                        @break
                                    @case('cerrado')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate_custom-100 text-slate_custom-600">Cerrado</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $solicitud->fecha_apertura?->format('d/m/Y') }}</td>
                            <td>{{ $solicitud->ejecutivo?->name ?? 'Sin asignar' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('servicios.atencion-cliente.show', $solicitud) }}" class="btn-secondary text-xs px-2 py-1" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('servicios.atencion-cliente.edit', $solicitud) }}" class="btn-secondary text-xs px-2 py-1" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('servicios.atencion-cliente.destroy', $solicitud) }}" method="POST" class="inline" onsubmit="return confirm('¿Esta seguro de eliminar esta solicitud?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary text-xs px-2 py-1 text-red-600 hover:text-red-800" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay solicitudes registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
