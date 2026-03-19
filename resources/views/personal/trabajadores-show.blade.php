<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Trabajador</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $trabajador->nombre_completo }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('personal.trabajadores.edit', $trabajador) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('personal.trabajadores.index') }}" class="btn-secondary">
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

    {{-- Informacion Personal --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>Informaci&oacute;n Personal
            </h3>
            <div>
                @switch($trabajador->estatus)
                    @case('A')
                        <span class="badge-success">Activo</span>
                        @break
                    @case('I')
                        <span class="badge-danger">Inactivo</span>
                        @break
                    @case('V')
                        <span class="badge-warning">Vacaciones</span>
                        @break
                    @default
                        <span class="badge-secondary">{{ $trabajador->estatus }}</span>
                @endswitch
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">C&eacute;dula</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->cedula ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Nombres</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->nombres }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Apellidos</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->apellidos }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha de Nacimiento</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->fecha_nacimiento?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Sexo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->sexo == 'M' ? 'Masculino' : ($trabajador->sexo == 'F' ? 'Femenino' : 'N/A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Email</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tel&eacute;fono</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->telefono ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Celular</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->celular ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Direcci&oacute;n</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->direccion ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Informacion Laboral --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-briefcase mr-2 text-burgundy-800"></i>Informaci&oacute;n Laboral
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Compa&ntilde;&iacute;a</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->compania?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Cargo</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->cargo ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Departamento</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->departamento ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Fecha de Ingreso</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ $trabajador->fecha_ingreso?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Salario B&aacute;sico</p>
                    <p class="text-sm font-medium text-navy-800 mt-1">{{ number_format($trabajador->salario_basico, 2, ',', '.') }} Bs</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Tipo de Contrato</p>
                    <p class="text-sm font-medium text-navy-800 mt-1 capitalize">{{ $trabajador->tipo_contrato ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial de Nomina --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-money-check-alt mr-2 text-burgundy-800"></i>Historial de N&oacute;mina
            </h3>
        </div>
        <div class="card-body p-0">
            @if($trabajador->nominaDetalles && $trabajador->nominaDetalles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>N&oacute;mina</th>
                                <th>D&iacute;as Trabajados</th>
                                <th>Salario Base</th>
                                <th>Asignaciones</th>
                                <th>Deducciones</th>
                                <th>Neto a Pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajador->nominaDetalles as $detalle)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $detalle->nomina?->codigo ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $detalle->dias_trabajados }}</td>
                                    <td class="text-right">{{ number_format($detalle->salario_base, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->total_asignaciones, 2, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($detalle->total_deducciones, 2, ',', '.') }}</td>
                                    <td class="text-right font-semibold">{{ number_format($detalle->neto_pagar, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-slate_custom-400">No hay registros de n&oacute;mina para este trabajador.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Prestaciones Sociales --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-hand-holding-usd mr-2 text-burgundy-800"></i>Prestaciones Sociales
            </h3>
        </div>
        <div class="card-body p-0">
            @if($trabajador->prestacionesSociales && $trabajador->prestacionesSociales->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajador->prestacionesSociales as $prestacion)
                                <tr>
                                    <td>{{ $prestacion->periodo ?? 'N/A' }}</td>
                                    <td class="text-right">{{ number_format($prestacion->monto ?? 0, 2, ',', '.') }} Bs</td>
                                    <td>{{ $prestacion->tipo ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge-info">{{ $prestacion->estatus ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-slate_custom-400">No hay registros de prestaciones sociales.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Vacaciones --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-umbrella-beach mr-2 text-burgundy-800"></i>Vacaciones
            </h3>
        </div>
        <div class="card-body p-0">
            @if($trabajador->vacaciones && $trabajador->vacaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>D&iacute;as Correspon.</th>
                                <th>D&iacute;as Disfrutados</th>
                                <th>Fecha Salida</th>
                                <th>Fecha Reincorporaci&oacute;n</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajador->vacaciones as $vacacion)
                                <tr>
                                    <td>{{ $vacacion->periodo_desde?->format('d/m/Y') }} - {{ $vacacion->periodo_hasta?->format('d/m/Y') }}</td>
                                    <td class="text-center">{{ $vacacion->dias_correspondientes }}</td>
                                    <td class="text-center">{{ $vacacion->dias_disfrutados }}</td>
                                    <td>{{ $vacacion->fecha_salida?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td>{{ $vacacion->fecha_reincorporacion?->format('d/m/Y') ?? 'N/A' }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-slate_custom-400">No hay registros de vacaciones.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
