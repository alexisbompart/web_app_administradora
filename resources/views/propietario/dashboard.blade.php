<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    Bienvenido, {{ $propietario->nombres }} {{ $propietario->apellidos }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-id-card mr-1"></i>CI: {{ $propietario->cedula }} &mdash; Portal del Propietario
                </p>
            </div>
            <div class="text-sm text-slate_custom-400">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ now()->format('d M, Y') }}
            </div>
        </div>
    </x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Apartamentos</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ $apartamentos->count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Unidades asociadas</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Deuda Total</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ number_format($totalDeuda, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Saldo pendiente total</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Meses Pendientes</div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-times text-yellow-600"></i>
                </div>
            </div>
            <div class="stat-value text-yellow-600">{{ $totalMeses }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Periodos sin cancelar</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Ultimo Pago</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600 text-lg">
                @if($pagosRecientes->isNotEmpty())
                    {{ number_format($pagosRecientes->first()->monto_aplicado, 2, ',', '.') }} Bs
                @else
                    --
                @endif
            </div>
            <p class="text-xs text-slate_custom-400 mt-1">
                @if($pagosRecientes->isNotEmpty())
                    {{ $pagosRecientes->first()->pago?->fecha_pago ? \Carbon\Carbon::parse($pagosRecientes->first()->pago->fecha_pago)->format('d/m/Y') : '--' }}
                @else
                    Sin pagos registrados
                @endif
            </p>
        </div>
    </div>

    {{-- Apartments Detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @foreach($apartamentos as $apto)
            @php
                $deudasApto = $deudasPorApto[$apto->id] ?? collect();
                $totalDeudaApto = $deudasApto->sum('saldo');
                $mesesMoroso = $deudasApto->count();
            @endphp
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-building mr-2 text-burgundy-800"></i>
                        {{ $apto->edificio->nombre }} - Apto {{ $apto->num_apto }}
                    </h3>
                    @if($mesesMoroso === 0)
                        <span class="badge-success">
                            <i class="fas fa-check-circle mr-1"></i>Al dia
                        </span>
                    @else
                        <span class="badge-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Moroso - {{ $mesesMoroso }} {{ $mesesMoroso === 1 ? 'mes' : 'meses' }}
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-slate_custom-100 rounded-lg p-3">
                            <p class="text-xs text-slate_custom-400">Alicuota</p>
                            <p class="text-sm font-semibold text-navy-800">{{ number_format($apto->alicuota, 2) }}%</p>
                        </div>
                        <div class="bg-slate_custom-100 rounded-lg p-3">
                            <p class="text-xs text-slate_custom-400">Area</p>
                            <p class="text-sm font-semibold text-navy-800">{{ number_format($apto->area_mts ?? 0, 2) }} m&sup2;</p>
                        </div>
                    </div>

                    @if($deudasApto->isNotEmpty())
                        <div class="border-t border-slate_custom-200 pt-3">
                            <p class="text-xs font-semibold text-slate_custom-500 uppercase mb-2">Deudas Pendientes</p>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($deudasApto->take(5) as $deuda)
                                    <div class="flex items-center justify-between text-sm py-1 border-b border-slate_custom-100 last:border-0">
                                        <span class="text-slate_custom-500">{{ $deuda->periodo }}</span>
                                        <span class="font-semibold text-red-600">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</span>
                                    </div>
                                @endforeach
                                @if($deudasApto->count() > 5)
                                    <p class="text-xs text-slate_custom-400 text-center">... y {{ $deudasApto->count() - 5 }} mas</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate_custom-200">
                                <span class="text-sm font-semibold text-navy-800">Total Deuda</span>
                                <span class="text-sm font-bold text-red-600">{{ number_format($totalDeudaApto, 2, ',', '.') }} Bs</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p class="text-sm text-slate_custom-400">Sin deudas pendientes</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent Payments Table --}}
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-history mr-2 text-burgundy-800"></i>Ultimos Pagos
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Apartamento</th>
                            <th>Periodo</th>
                            <th>Monto</th>
                            <th>Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagosRecientes as $pago)
                            <tr>
                                <td>{{ $pago->pago?->fecha_pago ? \Carbon\Carbon::parse($pago->pago->fecha_pago)->format('d/m/Y') : '--' }}</td>
                                <td class="font-medium">
                                    {{ $pago->apartamento->edificio->nombre }} - {{ $pago->apartamento->num_apto }}
                                </td>
                                <td>{{ $pago->periodo }}</td>
                                <td class="font-semibold text-green-600">{{ number_format($pago->monto_aplicado, 2, ',', '.') }} Bs</td>
                                <td>
                                    <a href="{{ route('mi-condominio.recibo', $pago->id) }}" class="text-burgundy-800 hover:text-navy-800 transition">
                                        <i class="fas fa-file-pdf mr-1"></i>Ver Recibo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate_custom-400 py-8">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No hay pagos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card bg-gradient-to-r from-navy-800 to-burgundy-800 text-white p-6">
        <h3 class="font-heading font-bold text-lg text-white mb-4">
            <i class="fas fa-bolt mr-2"></i>Acciones Rapidas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('mi-condominio.deudas') }}" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-white">Ver Deudas</p>
                    <p class="text-xs text-white/60">Consultar estado de cuenta</p>
                </div>
            </a>
            <a href="{{ route('mi-condominio.pagos') }}" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-white">Ver Pagos</p>
                    <p class="text-xs text-white/60">Historial de pagos realizados</p>
                </div>
            </a>
            <a href="{{ route('mi-condominio.estadisticas') }}" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-white">Estadisticas</p>
                    <p class="text-xs text-white/60">Informacion de mis edificios</p>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
