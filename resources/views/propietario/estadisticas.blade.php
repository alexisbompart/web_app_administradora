<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Estadisticas de mis Edificios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-user mr-1"></i>{{ $propietario->nombres }} {{ $propietario->apellidos }} - CI: {{ $propietario->cedula }}
                </p>
            </div>
            <a href="{{ route('mi-condominio.dashboard') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @foreach($stats as $stat)
        <div class="mb-8">
            {{-- Edificio Title --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-navy-800 to-burgundy-800 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white"></i>
                </div>
                <h3 class="text-lg font-heading font-bold text-navy-800">{{ $stat['edificio_nombre'] }}</h3>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div class="stat-label">Total Apartamentos</div>
                        <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-door-open text-navy-800"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stat['total_aptos'] }}</div>
                    <p class="text-xs text-slate_custom-400 mt-1">En el edificio</p>
                </div>

                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div class="stat-label">Deudas Pendientes</div>
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="stat-value text-red-600">{{ $stat['deudas_pendientes'] }}</div>
                    <p class="text-xs text-slate_custom-400 mt-1">Sin cancelar en el edificio</p>
                </div>

                @php
                    $morosidad = $stat['porcentaje_morosidad'] ?? 0;
                    if ($morosidad < 20) {
                        $morosidadColor = 'text-green-600';
                        $morosidadBg = 'bg-green-100';
                        $morosidadIcon = 'text-green-600';
                    } elseif ($morosidad <= 50) {
                        $morosidadColor = 'text-yellow-600';
                        $morosidadBg = 'bg-yellow-100';
                        $morosidadIcon = 'text-yellow-600';
                    } else {
                        $morosidadColor = 'text-red-600';
                        $morosidadBg = 'bg-red-100';
                        $morosidadIcon = 'text-red-600';
                    }
                @endphp
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div class="stat-label">% Morosidad</div>
                        <div class="w-10 h-10 {{ $morosidadBg }} rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-pie {{ $morosidadIcon }}"></i>
                        </div>
                    </div>
                    <div class="stat-value {{ $morosidadColor }}">{{ number_format($morosidad, 1) }}%</div>
                    <p class="text-xs text-slate_custom-400 mt-1">Tasa de morosidad</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Fondos --}}
                @if(!empty($stat['fondos']))
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-sm font-heading font-semibold text-navy-800">
                                <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Fondos del Edificio
                            </h4>
                        </div>
                        <div class="card-body space-y-3">
                            @foreach($stat['fondos'] as $fondo)
                                <div class="flex items-center justify-between py-2 border-b border-slate_custom-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-navy-800">{{ $fondo['nombre'] }}</p>
                                        <p class="text-xs text-slate_custom-400 capitalize">{{ $fondo['tipo'] }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-navy-800">
                                        {{ number_format($fondo['saldo'], 2, ',', '.') }} Bs
                                    </span>
                                </div>
                            @endforeach
                            @if(count($stat['fondos']) === 0)
                                <p class="text-sm text-slate_custom-400 text-center py-3">Sin fondos registrados</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- My Apartment Info --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-sm font-heading font-semibold text-navy-800">
                            <i class="fas fa-home mr-2 text-burgundy-800"></i>Mi Apartamento
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-slate_custom-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate_custom-400">Nro Apto</p>
                                <p class="text-lg font-bold text-navy-800">{{ $stat['num_apto'] }}</p>
                            </div>
                            <div class="bg-slate_custom-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate_custom-400">Alicuota</p>
                                <p class="text-lg font-bold text-burgundy-800">{{ number_format($stat['alicuota'], 2) }}%</p>
                            </div>
                            <div class="bg-slate_custom-100 rounded-lg p-3 text-center">
                                <p class="text-xs text-slate_custom-400">Area</p>
                                <p class="text-lg font-bold text-navy-800">{{ number_format($stat['area'], 2) }} m&sup2;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment History by Month --}}
            @if(!empty($stat['pagos_mensuales']))
                <div class="card mt-6">
                    <div class="card-header">
                        <h4 class="text-sm font-heading font-semibold text-navy-800">
                            <i class="fas fa-chart-bar mr-2 text-burgundy-800"></i>Historial de Pagos Mensuales
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="space-y-3">
                            @php
                                $maxPago = collect($stat['pagos_mensuales'])->max('total') ?: 1;
                            @endphp
                            @foreach($stat['pagos_mensuales'] as $mes)
                                @php
                                    $porcentaje = ($mes['total'] / $maxPago) * 100;
                                @endphp
                                <div class="flex items-center gap-4">
                                    <div class="w-24 text-xs font-medium text-slate_custom-500 text-right flex-shrink-0">
                                        {{ $mes['mes'] }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="w-full bg-slate_custom-100 rounded-full h-6 relative">
                                            <div class="bg-gradient-to-r from-navy-800 to-burgundy-800 h-6 rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                                                 style="width: {{ max($porcentaje, 5) }}%">
                                                @if($porcentaje > 20)
                                                    <span class="text-xs font-semibold text-white">{{ number_format($mes['total'], 2, ',', '.') }} Bs</span>
                                                @endif
                                            </div>
                                            @if($porcentaje <= 20)
                                                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate_custom-500">
                                                    {{ number_format($mes['total'], 2, ',', '.') }} Bs
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if(!$loop->last)
            <hr class="border-slate_custom-200 mb-8">
        @endif
    @endforeach

    @if(empty($stats))
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-bar text-2xl text-slate_custom-400"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin Estadisticas</h3>
                <p class="text-sm text-slate_custom-400">No hay datos estadisticos disponibles para tus apartamentos.</p>
            </div>
        </div>
    @endif
</x-app-layout>
