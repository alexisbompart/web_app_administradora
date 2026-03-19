<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle de Solicitud #{{ $solicitud->id }}</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $solicitud->asunto }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('servicios.atencion-cliente.edit', $solicitud) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('servicios.atencion-cliente.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Informacion General -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-info-circle mr-2 text-burgundy-800"></i>Informacion de la Solicitud
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Tipo</p>
                    <p class="text-sm font-medium text-navy-800">
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
                                {{ ucfirst($solicitud->tipo) }}
                        @endswitch
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Prioridad</p>
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
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estatus</p>
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
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Fecha Apertura</p>
                    <p class="text-sm font-medium text-navy-800">{{ $solicitud->fecha_apertura?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Fecha Cierre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $solicitud->fecha_cierre?->format('d/m/Y') ?? 'Pendiente' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Ejecutivo</p>
                    <p class="text-sm font-medium text-navy-800">{{ $solicitud->ejecutivo?->name ?? 'Sin asignar' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Edificio</p>
                    <p class="text-sm font-medium text-navy-800">{{ $solicitud->edificio?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Propietario</p>
                    <p class="text-sm font-medium text-navy-800">
                        @if($solicitud->propietario)
                            {{ $solicitud->propietario->nombres }} {{ $solicitud->propietario->apellidos }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Asunto y Descripcion -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-alt mr-2 text-burgundy-800"></i>Asunto y Descripcion
            </h3>
        </div>
        <div class="card-body">
            <h4 class="text-base font-heading font-semibold text-navy-800 mb-3">{{ $solicitud->asunto }}</h4>
            @if($solicitud->descripcion)
                <div class="bg-slate_custom-50 rounded-lg p-4">
                    <p class="text-sm text-slate_custom-600 whitespace-pre-line">{{ $solicitud->descripcion }}</p>
                </div>
            @else
                <p class="text-sm text-slate_custom-400 italic">Sin descripcion adicional.</p>
            @endif
        </div>
    </div>

    <!-- Respuesta -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-reply mr-2 text-burgundy-800"></i>Respuesta / Resolucion
            </h3>
        </div>
        <div class="card-body">
            @if($solicitud->respuesta)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800 whitespace-pre-line">{{ $solicitud->respuesta }}</p>
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-comment-slash text-3xl text-slate_custom-300 mb-2 block"></i>
                    <p class="text-sm text-slate_custom-400">Aun no se ha registrado una respuesta para esta solicitud.</p>
                    <a href="{{ route('servicios.atencion-cliente.edit', $solicitud) }}" class="btn-primary mt-3 inline-flex">
                        <i class="fas fa-edit mr-2"></i>Agregar Respuesta
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
