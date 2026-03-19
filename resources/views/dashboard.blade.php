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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Deudas Pendientes</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ \App\Models\Financiero\CondDeudaApto::where('estatus', 'P')->count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Pagos por cobrar</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Recibidos</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600">{{ \App\Models\Financiero\CondPago::count() }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Registrados este periodo</p>
        </div>
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
                    $morosos = \App\Models\Financiero\CondDeudaApto::where('estatus', 'P')
                        ->selectRaw('apartamento_id, COUNT(*) as meses, SUM(saldo) as total_deuda')
                        ->groupBy('apartamento_id')
                        ->having(\DB::raw('COUNT(*)'), '>=', 2)
                        ->orderByDesc('total_deuda')
                        ->take(5)
                        ->get();
                @endphp
                @forelse($morosos as $moroso)
                    @php $apto = \App\Models\Condominio\Apartamento::find($moroso->apartamento_id); @endphp
                    <div class="flex items-center justify-between py-2 border-b border-slate_custom-200 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-navy-800">Apto {{ $apto?->num_apto }}</p>
                            <p class="text-xs text-red-500">{{ $moroso->meses }} meses vencidos</p>
                        </div>
                        <span class="badge-danger">{{ number_format($moroso->total_deuda, 2, ',', '.') }} Bs</span>
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
</x-app-layout>
