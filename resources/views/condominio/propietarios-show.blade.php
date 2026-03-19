<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Propietario</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $propietario->nombres }} {{ $propietario->apellidos }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.propietarios.edit', $propietario) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('condominio.propietarios.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Propietario Details --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>Informacion del Propietario
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Cedula</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->cedula }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombres</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->nombres }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Apellidos</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->apellidos }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Telefono</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->telefono ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Celular</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->celular ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->email ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estatus</p>
                    @if($propietario->estatus)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-danger">Inactivo</span>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Direccion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $propietario->direccion ?? 'No registrada' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Apartments (Pivot) --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-door-open mr-2 text-burgundy-800"></i>Apartamentos Asociados
                <span class="badge-info ml-2">{{ $propietario->apartamentos->count() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            @if($propietario->apartamentos->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Edificio</th>
                                <th>Num. Apto</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Actual</th>
                                <th>Deuda Pendiente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($propietario->apartamentos as $apartamento)
                                <tr>
                                    <td>{{ $apartamento->edificio->nombre ?? 'N/A' }}</td>
                                    <td class="font-medium text-navy-800">{{ $apartamento->num_apto }}</td>
                                    <td>{{ $apartamento->pivot->fecha_desde ?? 'N/A' }}</td>
                                    <td>{{ $apartamento->pivot->fecha_hasta ?? 'Vigente' }}</td>
                                    <td>
                                        @if($apartamento->pivot->propietario_actual)
                                            <span class="badge-success">Si</span>
                                        @else
                                            <span class="badge-danger">No</span>
                                        @endif
                                    </td>
                                    <td class="font-medium">
                                        @php
                                            $totalDeuda = $apartamento->condDeudasApto->sum(function($d) {
                                                return ($d->monto ?? 0) - ($d->monto_pagado ?? 0);
                                            });
                                        @endphp
                                        @if($totalDeuda > 0)
                                            <span class="text-red-600">{{ number_format($totalDeuda, 2) }}</span>
                                        @else
                                            <span class="text-green-600">0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('condominio.apartamentos.show', $apartamento) }}" class="btn-secondary text-xs px-2 py-1">
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
                        <i class="fas fa-door-open text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin apartamentos asociados</h3>
                    <p class="text-sm text-slate_custom-400">Este propietario no tiene apartamentos registrados.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
