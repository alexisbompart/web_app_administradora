<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Facturacion por Edificio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">cond_movs_fact_edif</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('financiero.movfactedif.importar') }}" class="btn-primary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
                <a href="{{ route('admin.importaciones.index') }}" class="btn-primary">
                    <i class="fas fa-th-large mr-2"></i>Centro Importaciones
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Registros</div>
                <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Ultima Carga</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-sm">{{ $ultimaCarga ? \Carbon\Carbon::parse($ultimaCarga)->format('d/m/Y H:i') : 'Nunca' }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagina</div>
                <div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-slate_custom-500"></i>
                </div>
            </div>
            <div class="stat-value text-sm">{{ $items->currentPage() }} de {{ $items->lastPage() }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>Listado
            </h3>
        </div>
        <div class="card-body p-0">
            @if($items->count())
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Edificio</th>
                            <th>Facturacion</th>
                            <th>Cobranza</th>
                            <th>Deuda Actual</th>
                            <th>Deuda Anterior</th>
                            <th>Fecha Fact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                                <td class="text-xs">{{ $item->edificio?->nombre ?? $item->cod_edif_legacy }}</td>
                                <td class="text-xs font-semibold">{{ number_format($item->facturacion_edif ?? 0, 2, ',', '.') }}</td>
                                <td class="text-xs">{{ number_format($item->cobranza_edif ?? 0, 2, ',', '.') }}</td>
                                <td class="text-xs text-red-600">{{ number_format($item->deuda_act_edif ?? 0, 2, ',', '.') }}</td>
                                <td class="text-xs">{{ number_format($item->deuda_ant_edif ?? 0, 2, ',', '.') }}</td>
                                <td class="text-xs">{{ $item->fecha_fact?->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $items->links() }}</div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-2xl text-slate_custom-400"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin datos</h3>
                <p class="text-sm text-slate_custom-400 mb-4">No hay registros. Importe datos primero.</p>
                <a href="{{ route('financiero.movfactedif.importar') }}" class="btn-primary">
                    <i class="fas fa-file-import mr-2"></i>Importar
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>