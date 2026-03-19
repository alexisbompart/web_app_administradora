<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Detalle de Compania</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ $compania->nombre }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('condominio.companias.edit', $compania) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('condominio.companias.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Company Details --}}
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>Informacion de la Compania
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Codigo</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->cod_compania }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Nombre</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->nombre }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">RIF</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->rif }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Telefono</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->telefono ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->email ?? 'No registrado' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Estado</p>
                    @if($compania->activo)
                        <span class="badge-success">Activo</span>
                    @else
                        <span class="badge-danger">Inactivo</span>
                    @endif
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-1">Direccion</p>
                    <p class="text-sm font-medium text-navy-800">{{ $compania->direccion ?? 'No registrada' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Edificios --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-city mr-2 text-burgundy-800"></i>Edificios Asociados
                <span class="badge-info ml-2">{{ $compania->edificios->count() }}</span>
            </h3>
        </div>
        <div class="card-body p-0">
            @if($compania->edificios->count())
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Ciudad</th>
                                <th>Total Aptos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compania->edificios as $edificio)
                                <tr>
                                    <td class="font-medium text-navy-800">{{ $edificio->cod_edif }}</td>
                                    <td>{{ $edificio->nombre }}</td>
                                    <td>{{ $edificio->ciudad }}</td>
                                    <td>{{ $edificio->total_aptos }}</td>
                                    <td>
                                        @if($edificio->activo)
                                            <span class="badge-success">Activo</span>
                                        @else
                                            <span class="badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('condominio.edificios.show', $edificio) }}" class="btn-secondary text-xs px-2 py-1">
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
                        <i class="fas fa-city text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay edificios asociados</h3>
                    <p class="text-sm text-slate_custom-400">Esta compania aun no tiene edificios registrados.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
