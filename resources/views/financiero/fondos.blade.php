<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Fondos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de fondos y movimientos financieros</p>
            </div>
            <a href="{{ route('financiero.fondos.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nuevo Fondo
            </a>
        </div>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($fondos as $fondo)
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">{{ $fondo->nombre }}</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($fondo->saldo_actual, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1 capitalize">{{ $fondo->tipo }}</p>
        </div>
        @endforeach
    </div>

    <!-- Movimientos Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-exchange-alt mr-2 text-burgundy-800"></i>Movimientos de Fondos
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Fondo</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Saldo Anterior</th>
                            <th>Saldo Posterior</th>
                            <th>Referencia</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->fecha_movimiento?->format('d/m/Y') }}</td>
                            <td>{{ $movimiento->fondo?->nombre }}</td>
                            <td>
                                @if($movimiento->tipo_movimiento === 'ingreso')
                                    <span class="badge-success">Ingreso</span>
                                @else
                                    <span class="badge-danger">Egreso</span>
                                @endif
                            </td>
                            <td class="font-semibold">{{ number_format($movimiento->monto, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($movimiento->saldo_anterior, 2, ',', '.') }} Bs</td>
                            <td>{{ number_format($movimiento->saldo_posterior, 2, ',', '.') }} Bs</td>
                            <td>{{ $movimiento->referencia }}</td>
                            <td>{{ Str::limit($movimiento->descripcion, 40) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay movimientos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
