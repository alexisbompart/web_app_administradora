<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Cobranza</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de deudas y cobranza de apartamentos</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.cobranza.morosos') }}" class="btn-secondary">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Morosos
                </a>
                <a href="{{ route('financiero.cobranza.judicial') }}" class="btn-secondary">
                    <i class="fas fa-gavel mr-2"></i>Gestión Judicial
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Deudas Pendientes</div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-yellow-600"></i>
                </div>
            </div>
            <div class="stat-value">{{ $stats['pendientes_count'] }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Deudas sin cancelar</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Monto Total Pendiente</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ number_format($stats['monto_total_pendiente'], 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Saldo por cobrar</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Morosos (3+ meses)</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-slash text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-burgundy-800">{{ $stats['morosos_count'] }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Apartamentos en mora</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Listado de Deudas
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('financiero.cobranza.index') }}"
                   class="px-3 py-1 rounded-lg text-sm font-medium {{ !request('filtro') ? 'bg-navy-800 text-white' : 'bg-slate_custom-100 text-slate_custom-500 hover:bg-slate_custom-200' }}">
                    Todas
                </a>
                <a href="{{ route('financiero.cobranza.index', ['filtro' => 'pendientes']) }}"
                   class="px-3 py-1 rounded-lg text-sm font-medium {{ request('filtro') === 'pendientes' ? 'bg-navy-800 text-white' : 'bg-slate_custom-100 text-slate_custom-500 hover:bg-slate_custom-200' }}">
                    Pendientes
                </a>
                <a href="{{ route('financiero.cobranza.index', ['filtro' => 'canceladas']) }}"
                   class="px-3 py-1 rounded-lg text-sm font-medium {{ request('filtro') === 'canceladas' ? 'bg-navy-800 text-white' : 'bg-slate_custom-100 text-slate_custom-500 hover:bg-slate_custom-200' }}">
                    Canceladas
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Apartamento</th>
                            <th>Edificio</th>
                            <th>Periodo</th>
                            <th>Monto Original</th>
                            <th>Monto Pagado</th>
                            <th>Saldo</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deudas as $deuda)
                        <tr>
                            <td class="font-medium">{{ $deuda->apartamento?->num_apto }}</td>
                            <td>{{ $deuda->edificio?->nombre }}</td>
                            <td>{{ $deuda->periodo }}</td>
                            <td>{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($deuda->monto_pagado, 2, ',', '.') }} Bs</td>
                            <td class="font-semibold">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                            <td>
                                @if($deuda->estatus === 'P')
                                    <span class="badge-warning">Pendiente</span>
                                @elseif($deuda->estatus === 'C')
                                    <span class="badge-success">Cancelada</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Registrar pago">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay deudas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $deudas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
