<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle del Edificio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $edificio->nombre }} - {{ $edificio->compania->nombre ?? 'Sin compania' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.edificios.edit', $edificio) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('condominio.edificios.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card">
            <div class="card-body text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-door-open text-blue-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-navy-800">{{ $edificio->total_aptos ?? 0 }}</p>
                <p class="text-xs text-slate_custom-400">Total Apartamentos</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-navy-800">{{ $edificio->apartamentos->count() }}</p>
                <p class="text-xs text-slate_custom-400">Aptos Registrados</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-percentage text-yellow-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-navy-800">{{ $edificio->alicuota_base ?? '0.00' }}</p>
                <p class="text-xs text-slate_custom-400">Alicuota Base</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <p class="text-2xl font-bold text-navy-800">{{ $edificio->mora_porcentaje ?? '0.00' }}%</p>
                <p class="text-xs text-slate_custom-400">Mora</p>
            </div>
        </div>
    </div>

    {{-- Building Details --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-city mr-2 text-burgundy-800"></i>Informacion del Edificio
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Codigo</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->cod_edif }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Compania</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->compania->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->nombre }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">RIF</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->rif ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Ciudad</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->ciudad ?? 'No registrada' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Telefono</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->telefono ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->email ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Dia Corte</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->dia_corte ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Dia Vencimiento</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->dia_vencimiento ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Fondo Reserva</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->fondo_reserva_porcentaje ?? '0.00' }}%</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estado</p>
                    @if($edificio->activo)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-danger">Inactivo</span>
                    @endif
                </div>
                <div class="lg:col-span-3">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Direccion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $edificio->direccion ?? 'No registrada' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Apartamentos List --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-door-open mr-2 text-burgundy-800"></i>Apartamentos
                <span class="badge-info ml-2">{{ $edificio->apartamentos->count() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            @if($edificio->apartamentos->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Num. Apto</th>
                                <th>Piso</th>
                                <th>Propietario</th>
                                <th>Alicuota</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($edificio->apartamentos as $apartamento)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $apartamento->num_apto }}</td>
                                    <td>{{ $apartamento->piso }}</td>
                                    <td>{{ $apartamento->propietario_nombre ?? 'Sin propietario' }}</td>
                                    <td>{{ $apartamento->alicuota }}</td>
                                    <td>
                                        @if($apartamento->estatus == 'A')
                                            <span class="badge-success">Activo</span>
                                        @elseif($apartamento->estatus == 'I')
                                            <span class="badge-danger">Inactivo</span>
                                        @else
                                            <span class="badge-warning">Moroso</span>
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
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay apartamentos registrados</h3>
                    <p class="text-sm text-slate_custom-400">Este edificio aun no tiene apartamentos registrados.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
