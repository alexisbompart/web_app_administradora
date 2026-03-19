<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Apartamento</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Apto {{ $apartamento->num_apto }} - {{ $apartamento->edificio->nombre ?? 'Sin edificio' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.apartamentos.edit', $apartamento) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('condominio.apartamentos.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Apartment Details --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-door-open mr-2 text-burgundy-800"></i>Informacion del Apartamento
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Edificio</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->edificio->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Num. Apartamento</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->num_apto }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Piso</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->piso ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estatus</p>
                    @if($apartamento->estatus == 'A')
                        <span class="badge-success">Activo</span>
                    @elseif($apartamento->estatus == 'I')
                        <span class="badge-danger">Inactivo</span>
                    @else
                        <span class="badge-warning">Moroso</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Area (m2)</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->area_mts ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Alicuota</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->alicuota ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Habitaciones</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->habitaciones ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Banos</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->banos ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estacionamiento</p>
                    @if($apartamento->estacionamiento)
                        <span class="badge-success">Si</span>
                    @else
                        <span class="badge-danger">No</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Owner Info --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>Propietario Actual
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->propietario_nombre ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Cedula</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->propietario_cedula ?? 'No registrada' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Telefono</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->propietario_telefono ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm font-medium text-navy-800">{{ $apartamento->propietario_email ?? 'No registrado' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Propietarios History (Pivot) --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-history mr-2 text-burgundy-800"></i>Historial de Propietarios
                <span class="badge-info ml-2">{{ $apartamento->propietarios->count() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            @if($apartamento->propietarios->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Actual</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartamento->propietarios as $propietario)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $propietario->cedula }}</td>
                                    <td>{{ $propietario->nombres }} {{ $propietario->apellidos }}</td>
                                    <td>{{ $propietario->pivot->fecha_desde ?? 'N/A' }}</td>
                                    <td>{{ $propietario->pivot->fecha_hasta ?? 'Vigente' }}</td>
                                    <td>
                                        @if($propietario->pivot->propietario_actual)
                                            <span class="badge-success">Si</span>
                                        @else
                                            <span class="badge-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('condominio.propietarios.show', $propietario) }}" class="btn-secondary text-xs px-2 py-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin historial de propietarios</h3>
                    <p class="text-sm text-slate_custom-400">No se han registrado propietarios en la tabla pivote.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Deudas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Deudas del Apartamento
                <span class="badge-info ml-2">{{ $apartamento->condDeudasApto->count() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            @if($apartamento->condDeudasApto->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Monto</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartamento->condDeudasApto as $deuda)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $deuda->periodo ?? $deuda->mes.'/'.$deuda->anio }}</td>
                                    <td>{{ number_format($deuda->monto ?? 0, 2) }}</td>
                                    <td>{{ number_format($deuda->monto_pagado ?? 0, 2) }}</td>
                                    <td class="font-medium">{{ number_format(($deuda->monto ?? 0) - ($deuda->monto_pagado ?? 0), 2) }}</td>
                                    <td>
                                        @if(($deuda->monto ?? 0) <= ($deuda->monto_pagado ?? 0))
                                            <span class="badge-success">Pagado</span>
                                        @else
                                            <span class="badge-warning">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-invoice-dollar text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin deudas registradas</h3>
                    <p class="text-sm text-slate_custom-400">Este apartamento no tiene deudas registradas.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
