<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Cobranza</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de deudas y cobranza de apartamentos</p>
            </div>
            <div class="flex items-center gap-3">
                @php $pagosPendCount = \App\Models\Financiero\CondPago::where('estatus','P')->count(); @endphp
                <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="btn-primary relative">
                    <i class="fas fa-clock mr-2"></i>Pagos Pendientes
                    @if($pagosPendCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ $pagosPendCount }}</span>
                    @endif
                </a>
                <a href="{{ route('financiero.cobranza.morosos') }}" class="btn-secondary">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Morosos
                </a>
                <a href="{{ route('financiero.cobranza.judicial') }}" class="btn-secondary">
                    <i class="fas fa-gavel mr-2"></i>Gestion Judicial
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Panel de Importacion --}}
    <div class="card mb-6" x-data="{ showImport: false }">
        <div class="card-header cursor-pointer flex items-center justify-between" @click="showImport = !showImport">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-import mr-2 text-burgundy-800"></i>Modulos de Importacion
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate_custom-400">9 modulos disponibles</span>
                <i class="fas text-slate_custom-400" :class="showImport ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </div>
        </div>
        <div class="card-body" x-show="showImport" x-transition>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('financiero.deudas.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-red-200 transition">
                        <i class="fas fa-file-invoice-dollar text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Deudas</p>
                        <p class="text-xs text-slate_custom-400">cond_deudas_apto</p>
                    </div>
                </a>
                <a href="{{ route('financiero.descuentos.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 transition">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Descuentos</p>
                        <p class="text-xs text-slate_custom-400">cond_descuentos_apto</p>
                    </div>
                </a>
                <a href="{{ route('financiero.abonos.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Abonos</p>
                        <p class="text-xs text-slate_custom-400">cond_abonos_apto</p>
                    </div>
                </a>
                <a href="{{ route('financiero.gastos.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition">
                        <i class="fas fa-receipt text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Gastos</p>
                        <p class="text-xs text-slate_custom-400">cond_gastos</p>
                    </div>
                </a>
                <a href="{{ route('financiero.pagos.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition">
                        <i class="fas fa-money-check-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Pagos</p>
                        <p class="text-xs text-slate_custom-400">cond_pagos</p>
                    </div>
                </a>
                <a href="{{ route('financiero.pagoapto.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-cyan-200 transition">
                        <i class="fas fa-credit-card text-cyan-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Pagos por Apto</p>
                        <p class="text-xs text-slate_custom-400">cond_pago_aptos</p>
                    </div>
                </a>
                <a href="{{ route('financiero.movprefact.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-orange-200 transition">
                        <i class="fas fa-exchange-alt text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Mov. Pre-facturacion</p>
                        <p class="text-xs text-slate_custom-400">cond_movimientos_prefact</p>
                    </div>
                </a>
                <a href="{{ route('financiero.movfactapto.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 transition">
                        <i class="fas fa-file-invoice text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Fact. por Apto</p>
                        <p class="text-xs text-slate_custom-400">cond_movs_fact_apto</p>
                    </div>
                </a>
                <a href="{{ route('financiero.movfactedif.importar') }}" class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl hover:bg-slate_custom-50 hover:border-burgundy-800/30 transition group">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-teal-200 transition">
                        <i class="fas fa-building text-teal-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-navy-800">Fact. por Edificio</p>
                        <p class="text-xs text-slate_custom-400">cond_movs_fact_edif</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="stat-card hover:border-amber-400 transition cursor-pointer">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos por Aprobar</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ $pagosPendCount }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Registrados por propietarios</p>
        </a>
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
