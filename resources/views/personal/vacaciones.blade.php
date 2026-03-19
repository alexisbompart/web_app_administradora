<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Vacaciones</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Control de vacaciones del personal</p>
            </div>
            <a href="{{ route('personal.vacaciones.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Crear Vacaci&oacute;n
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-umbrella-beach mr-2 text-burgundy-800"></i>Listado de Vacaciones
            </h3>
        </div>
        <div class="card-body p-0">
            @if($vacaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Trabajador</th>
                                <th>Periodo</th>
                                <th>D&iacute;as Correspon.</th>
                                <th>D&iacute;as Disfrutados</th>
                                <th>Fecha Salida</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacaciones as $vacacion)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $vacacion->trabajador?->nombre_completo ?? 'N/A' }}</td>
                                    <td>{{ $vacacion->periodo_desde?->format('d/m/Y') }} - {{ $vacacion->periodo_hasta?->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $vacacion->dias_correspondientes }}</td>
                                    <td class="text-center">{{ $vacacion->dias_disfrutados }}</td>
                                    <td>{{ $vacacion->fecha_salida?->format('d/m/Y') }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('personal.vacaciones.show', $vacacion) }}" class="btn-secondary btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('personal.vacaciones.edit', $vacacion) }}" class="btn-secondary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('personal.vacaciones.destroy', $vacacion) }}" method="POST" onsubmit="return confirm('&iquest;Est&aacute; seguro de eliminar esta vacaci&oacute;n?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary btn-sm text-red-600 hover:text-red-800" title="Eliminar">
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
                <div class="px-6 py-4">
                    {{ $vacaciones->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-umbrella-beach text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay vacaciones registradas</h3>
                    <p class="text-sm text-slate_custom-400 mb-4">Comience registrando las vacaciones del personal.</p>
                    <a href="{{ route('personal.vacaciones.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Crear Vacaci&oacute;n
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
