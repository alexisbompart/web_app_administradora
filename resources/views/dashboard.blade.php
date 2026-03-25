<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Dashboard</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Resumen general del sistema de condominio</p>
            </div>
            <div class="text-sm text-slate_custom-400">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ now()->format('d M, Y') }}
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Edificios</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-city text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Condominio\Edificio::count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Registrados en el sistema</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Apartamentos</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ \App\Models\Condominio\Apartamento::count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Total de unidades</p>
        </div>

        @php
            $morososQuery = \App\Models\Financiero\CondDeudaApto::whereHas('edificio', function($q) {
                $q->where('activo', true);
            })->where(function($q) {
                $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
            })->where(function($q) {
                $q->whereNull('serial')->orWhere('serial', 'N');
            });
            $totalDeudas = $morososQuery->count();
            $totalMontoPendiente = $morososQuery->sum('saldo');
            $aptosEnMora = (clone $morososQuery)->distinct('apartamento_id')->count('apartamento_id');
        @endphp
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Deudas Pendientes</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ $totalDeudas }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">{{ number_format($totalMontoPendiente, 2, ',', '.') }} Bs en {{ $aptosEnMora }} aptos</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Aprobados</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600">{{ number_format($totalPagosAprobados, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Total cobrado</p>
        </div>
        <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="stat-card hover:border-amber-400 transition">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Pendientes</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ $countPagosPendientes }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Por aprobar</p>
        </a>
    </div>

    <!-- Second row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Fondos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Fondos
                </h3>
            </div>
            <div class="card-body space-y-4">
                @foreach(\App\Models\Financiero\Fondo::where('activo', true)->get() as $fondo)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-navy-800">{{ $fondo->nombre }}</p>
                        <p class="text-xs text-slate_custom-400 capitalize">{{ $fondo->tipo }}</p>
                    </div>
                    <span class="text-sm font-bold text-navy-800">
                        {{ number_format($fondo->saldo_actual, 2, ',', '.') }} Bs
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Morosos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>Apartamentos Morosos
                </h3>
            </div>
            <div class="card-body">
                @php
                    $morosos = \App\Models\Financiero\CondDeudaApto::whereHas('edificio', function($q) {
                            $q->where('activo', true);
                        })
                        ->where(function($q) {
                            $q->whereNull('fecha_pag')->orWhere('fecha_pag', '0001-01-01');
                        })
                        ->where(function($q) {
                            $q->whereNull('serial')->orWhere('serial', 'N');
                        })
                        ->selectRaw('apartamento_id, COUNT(*) as meses, SUM(saldo) as total_deuda')
                        ->groupBy('apartamento_id')
                        ->orderByDesc('total_deuda')
                        ->take(10)
                        ->get();
                @endphp
                @forelse($morosos as $moroso)
                    @php $apto = \App\Models\Condominio\Apartamento::with('edificio')->find($moroso->apartamento_id); @endphp
                    <div class="flex items-center justify-between py-2 border-b border-slate_custom-200 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-navy-800">{{ $apto?->edificio?->nombre }} - {{ $apto?->num_apto }}</p>
                            <p class="text-xs text-red-500">{{ $moroso->meses }} {{ $moroso->meses == 1 ? 'mes' : 'meses' }} pendiente{{ $moroso->meses > 1 ? 's' : '' }}</p>
                        </div>
                        <span class="badge-danger text-xs">{{ number_format($moroso->total_deuda, 2, ',', '.') }} Bs</span>
                    </div>
                @empty
                    <p class="text-sm text-slate_custom-400 text-center py-4">No hay morosos registrados</p>
                @endforelse
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>Acceso Rápido
                </h3>
            </div>
            <div class="card-body grid grid-cols-2 gap-3">
                @can('cobranza.registrar-pago')
                <a href="{{ route('financiero.cobranza.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-hand-holding-usd text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Registrar Pago</span>
                </a>
                @endcan
                @can('informes.ver')
                <a href="{{ route('servicios.informes.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-chart-bar text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Ver Informes</span>
                </a>
                @endcan
                @can('proveedores.ver')
                <a href="{{ route('proveedores.facturas.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-file-invoice-dollar text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Facturas</span>
                </a>
                @endcan
                @can('personal.ver')
                <a href="{{ route('personal.nominas.index') }}" class="flex flex-col items-center p-4 rounded-lg bg-slate_custom-100 hover:bg-burgundy-800 hover:text-white text-navy-800 transition group">
                    <i class="fas fa-money-check-alt text-xl mb-2 group-hover:text-white"></i>
                    <span class="text-xs font-medium text-center">Nóminas</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Pagos por mes + Pendientes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Gráfico pagos por mes --}}
        <div class="card lg:col-span-2">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-chart-bar mr-2 text-burgundy-800"></i>Pagos Recibidos por Mes
                </h3>
                <span class="text-xs text-slate_custom-400">Últimos 12 meses</span>
            </div>
            <div class="card-body">
                <canvas id="chartPagosMes" height="110"></canvas>
            </div>
        </div>

        {{-- Pagos pendientes --}}
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-clock mr-2 text-amber-500"></i>Pagos por Aprobar
                </h3>
                @if($countPagosPendientes > 0)
                <span class="text-xs bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">{{ $countPagosPendientes }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @forelse($pagosPendientes as $pago)
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate_custom-100 last:border-0 hover:bg-slate_custom-50 transition">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-navy-800 truncate">
                            {{ $pago->registradoPor?->name ?? 'Propietario' }}
                        </p>
                        <p class="text-xs text-slate_custom-400">
                            {{ $pago->fecha_pago?->format('d/m/Y') }}
                            · Ref: {{ $pago->numero_referencia }}
                        </p>
                        <p class="text-xs text-slate_custom-400 truncate">
                            @foreach($pago->condPagoAptos->unique('apartamento_id')->take(2) as $pa)
                                {{ $pa->apartamento?->num_apto }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right ml-3">
                        <p class="text-sm font-bold text-amber-600">{{ number_format($pago->monto_total, 2, ',', '.') }}</p>
                        <a href="{{ route('financiero.cobranza.pagos-pendientes') }}"
                           class="text-xs text-burgundy-800 hover:underline font-medium">Revisar</a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate_custom-400">
                    <i class="fas fa-check-circle text-2xl text-green-400 mb-2 block"></i>
                    <p class="text-sm">Sin pagos pendientes</p>
                </div>
                @endforelse
                @if($countPagosPendientes > 0)
                <div class="px-4 py-2 border-t border-slate_custom-100">
                    <a href="{{ route('financiero.cobranza.pagos-pendientes') }}" class="text-xs text-burgundy-800 font-semibold hover:underline">
                        Ver todos ({{ $countPagosPendientes }}) <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info banner -->
    <div class="card bg-gradient-to-r from-burgundy-800 to-navy-800 text-white p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-2xl"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-lg text-white">Bienvenido, {{ Auth::user()->name }}</h3>
                <p class="text-white/70 text-sm mt-1">
                    Rol: <span class="font-semibold text-white">{{ Auth::user()->roles->first()?->name ?? 'Sin rol' }}</span>
                    &mdash; Sistema de Administración de Condominios v1.0
                </p>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const labels  = @json($mesesLabels);
        const montos  = @json($mesesMonto);
        const counts  = @json($mesesCantidad);

        new Chart(document.getElementById('chartPagosMes'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Monto (Bs)',
                        data: montos,
                        backgroundColor: 'rgba(127,0,55,0.75)',
                        borderColor: 'rgba(127,0,55,1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'yMonto',
                    },
                    {
                        label: 'Cantidad',
                        data: counts,
                        type: 'line',
                        borderColor: '#1e3a5f',
                        backgroundColor: 'rgba(30,58,95,0.1)',
                        pointBackgroundColor: '#1e3a5f',
                        pointRadius: 4,
                        tension: 0.3,
                        yAxisID: 'yCant',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.yAxisID === 'yMonto'
                                ? ' ' + ctx.parsed.y.toLocaleString('es-VE', {minimumFractionDigits:2}) + ' Bs'
                                : ' ' + ctx.parsed.y + ' pagos'
                        }
                    }
                },
                scales: {
                    yMonto: { position: 'left',  grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                    yCant:  { position: 'right', grid: { display: false },   ticks: { font: { size: 10 } } }
                }
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
