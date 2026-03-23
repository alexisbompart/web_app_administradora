<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Facturacion por Apartamento</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Movimientos de facturacion por apartamento</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('financiero.movfactapto.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar Fact Apto
                </a>
                <a href="{{ route('admin.importaciones.index') }}" class="btn-secondary">
                    <i class="fas fa-th-large mr-2"></i>Centro Importaciones
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Registros</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Facturaciones por apartamento</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Ultima Carga</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-sm">{{ $ultimaCarga ?? 'Sin datos' }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Fecha de ultima importacion</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Tabla Origen</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-database text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-sm">cond_movs_fact_apto</div>
            <p class="text-xs text-slate_custom-400 mt-1">Fuente de datos</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice mr-2 text-burgundy-800"></i>Listado de Facturacion por Apartamento
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Edificio</th>
                            <th>Apartamento</th>
                            <th>Propietario</th>
                            <th>Pago Total</th>
                            <th>Fecha Fact</th>
                            <th>Meses Deuda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->edificio?->nombre ?? 'N/A' }}</td>
                            <td class="font-medium">{{ $item->apartamento?->num_apto ?? 'N/A' }}</td>
                            <td>{{ $item->propietario ?? 'N/A' }}</td>
                            <td class="font-semibold">{{ number_format($item->pago_total, 2, ',', '.') }}</td>
                            <td>{{ $item->fecha_fact?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>
                                @if($item->meses_deuda > 2)
                                    <span class="badge-danger">{{ $item->meses_deuda }}</span>
                                @elseif($item->meses_deuda > 0)
                                    <span class="badge-warning">{{ $item->meses_deuda }}</span>
                                @else
                                    <span class="badge-success">{{ $item->meses_deuda ?? 0 }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay facturaciones por apartamento registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
