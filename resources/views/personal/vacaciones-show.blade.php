<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle de Vacaci&oacute;n</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $vacacion->trabajador?->nombre_completo ?? 'N/A' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personal.vacaciones.edit', $vacacion) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('personal.vacaciones.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Informacion del Trabajador --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>Informaci&oacute;n del Trabajador
            </h3>
            <div>
                @switch($vacacion->estatus)
                    @case('aprobada')
                        <span class="badge-success">Aprobada</span>
                        @break
                    @case('pendiente')
                        <span class="badge-warning">Pendiente</span>
                        @break
                    @case('rechazada')
                        <span class="badge-danger">Rechazada</span>
                        @break
                    @case('disfrutada')
                        <span class="badge-info">Disfrutada</span>
                        @break
                    @default
                        <span class="badge-secondary">{{ $vacacion->estatus }}</span>
                @endswitch
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Trabajador</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->trabajador?->nombre_completo ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">C&eacute;dula</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->trabajador?->cedula ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Cargo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->trabajador?->cargo ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalle de Vacaciones --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-umbrella-beach mr-2 text-burgundy-800"></i>Detalle de Vacaciones
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Periodo Desde</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->periodo_desde?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Periodo Hasta</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->periodo_hasta?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">D&iacute;as Correspondientes</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->dias_correspondientes ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">D&iacute;as Disfrutados</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->dias_disfrutados ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">D&iacute;as Pendientes</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->dias_pendientes ?? ($vacacion->dias_correspondientes - $vacacion->dias_disfrutados) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha de Salida</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->fecha_salida?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha de Reincorporaci&oacute;n</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->fecha_reincorporacion?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Suplente</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->suplente?->nombre_completo ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Monto Bono Vacacional</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->monto_bono_vacacional ? number_format($vacacion->monto_bono_vacacional, 2, ',', '.') . ' Bs' : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Aprobado Por</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $vacacion->aprobadoPor?->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
