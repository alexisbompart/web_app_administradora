<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Mis Deudas de Condominio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-user mr-1"></i>{{ $propietario->nombres }} {{ $propietario->apellidos }} - CI: {{ $propietario->cedula }}
                </p>
            </div>
            <a href="{{ route('mi-condominio.dashboard') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    {{-- Filter Bar --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('mi-condominio.deudas') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Apartamento</label>
                    <select name="apartamento_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todos los apartamentos</option>
                        @foreach($apartamentos as $apto)
                            <option value="{{ $apto->id }}" {{ request('apartamento_id') == $apto->id ? 'selected' : '' }}>
                                {{ $apto->edificio->nombre }} - Apto {{ $apto->num_apto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Estatus</label>
                    <select name="estatus" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todas</option>
                        <option value="P" {{ request('estatus') === 'P' ? 'selected' : '' }}>Pendientes</option>
                        <option value="C" {{ request('estatus') === 'C' ? 'selected' : '' }}>Canceladas</option>
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

    {{-- Summary --}}
    @php
        $pendientes = $deudas->getCollection()->where('estatus', 'P');
        $totalPendiente = $pendientes->sum('saldo');
        $countPendiente = $pendientes->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Deudas Pendientes</div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
            </div>
            <div class="stat-value text-yellow-600">{{ $countPendiente }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Monto Total Pendiente</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ number_format($totalPendiente, 2, ',', '.') }} Bs</div>
        </div>
    </div>

    {{-- Debts Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Listado de Deudas
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Apartamento</th>
                            <th>Periodo</th>
                            <th>Fecha Emision</th>
                            <th>Fecha Vencimiento</th>
                            <th>Monto Original</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deudas as $deuda)
                            @php
                                $vencida = $deuda->estatus === 'P' && $deuda->fecha_vencimiento && \Carbon\Carbon::parse($deuda->fecha_vencimiento)->lt(now());
                            @endphp
                            <tr class="{{ $vencida ? 'bg-red-50' : '' }}">
                                <td class="font-medium">
                                    {{ $deuda->apartamento->edificio->nombre }} - {{ $deuda->apartamento->num_apto }}
                                </td>
                                <td>{{ $deuda->periodo }}</td>
                                <td>{{ $deuda->fecha_emision ? \Carbon\Carbon::parse($deuda->fecha_emision)->format('d/m/Y') : '--' }}</td>
                                <td>
                                    {{ $deuda->fecha_vencimiento ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)->format('d/m/Y') : '--' }}
                                    @if($vencida)
                                        <span class="text-xs text-red-500 block">
                                            <i class="fas fa-clock mr-1"></i>Vencida
                                        </span>
                                    @endif
                                </td>
                                <td>{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                <td>{{ number_format($deuda->monto_pagado, 2, ',', '.') }} Bs</td>
                                <td class="font-semibold {{ $deuda->saldo > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($deuda->saldo, 2, ',', '.') }} Bs
                                </td>
                                <td>
                                    @if($deuda->estatus === 'P')
                                        <span class="badge-warning">Pendiente</span>
                                    @elseif($deuda->estatus === 'C')
                                        <span class="badge-success">Cancelada</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('mi-condominio.recibo-condominio', $deuda->id) }}"
                                       class="text-burgundy-800 hover:text-navy-800 transition text-sm">
                                        <i class="fas fa-file-alt mr-1"></i>Ver Recibo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-slate_custom-400 py-8">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No hay deudas registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $deudas->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
