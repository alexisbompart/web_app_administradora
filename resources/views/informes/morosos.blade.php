<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Listado de Morosos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Apartamentos con pagos pendientes o vencidos</p>
            </div>
            <a href="{{ route('servicios.informes.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Informes
            </a>
        </div>
    </x-slot>

    <!-- Filter Form -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-filter mr-2 text-burgundy-800"></i>Filtros
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.informes.morosos') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Edificio</label>
                        <select name="edificio_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Todos los Edificios --</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ request('edificio_id') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Meses M&iacute;nimos Vencidos</label>
                        <input type="number" name="meses" value="{{ request('meses', 1) }}" min="1" max="60" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Morosos</p>
                    <p class="text-2xl font-heading font-bold text-red-600">{{ $morosos->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Monto Total Adeudado</p>
                    <p class="text-2xl font-heading font-bold text-burgundy-800">{{ number_format($morosos->sum('total_deuda'), 2, ',', '.') }} Bs</p>
                </div>
                <div class="w-12 h-12 bg-burgundy-800/10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-burgundy-800"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Morosos Table -->
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Detalle de Morosos
            </h3>
            <button onclick="alert('Funcionalidad de exportaci&oacute;n pr&oacute;ximamente disponible.')" class="btn-secondary text-xs">
                <i class="fas fa-file-export mr-1"></i>Exportar
            </button>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Edificio</th>
                            <th>Apartamento</th>
                            <th>Propietario</th>
                            <th>Meses Vencidos</th>
                            <th>Monto Adeudado</th>
                            <th>&Uacute;ltimo Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($morosos as $moroso)
                        <tr>
                            <td>{{ $moroso->edificio_nombre }}</td>
                            <td class="font-medium">{{ $moroso->num_apto }}</td>
                            <td>{{ $moroso->propietario_nombre }}</td>
                            <td>
                                @if($moroso->meses_vencidos >= 3)
                                    <span class="badge-danger">{{ $moroso->meses_vencidos }} meses</span>
                                @else
                                    <span class="badge-warning">{{ $moroso->meses_vencidos }} {{ $moroso->meses_vencidos == 1 ? 'mes' : 'meses' }}</span>
                                @endif
                            </td>
                            <td class="font-semibold text-red-600">{{ number_format($moroso->total_deuda, 2, ',', '.') }} Bs</td>
                            <td>{{ $moroso->ultimo_pago ?? 'Sin pagos' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-check-circle text-3xl mb-2 block text-green-400"></i>
                                No se encontraron morosos con los filtros seleccionados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
