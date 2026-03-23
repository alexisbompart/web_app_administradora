<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Deudas por Apartamento</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Registro de deudas de condominio por apartamento y periodo</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('financiero.deudas.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar Deudas
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
                    <i class="fas fa-file-invoice-dollar text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Deudas registradas</p>
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
                <div class="stat-label">Pendientes</div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
            </div>
            <div class="stat-value text-yellow-600">{{ number_format($items->where('estatus', 'P')->count()) }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">En esta pagina</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="" class="flex items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-navy-800">Estatus</label>
                    <select name="estatus" class="form-select mt-1 text-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="P" {{ request('estatus') == 'P' ? 'selected' : '' }}>Pendiente</option>
                        <option value="C" {{ request('estatus') == 'C' ? 'selected' : '' }}>Cancelada</option>
                        <option value="X" {{ request('estatus') == 'X' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Listado de Deudas
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Edificio</th>
                            <th>Apartamento</th>
                            <th>Periodo</th>
                            <th>Monto Original</th>
                            <th>Saldo</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->edificio?->nombre ?? 'N/A' }}</td>
                            <td class="font-medium">{{ $item->apartamento?->num_apto ?? 'N/A' }}</td>
                            <td>{{ $item->periodo ?? 'N/A' }}</td>
                            <td class="font-semibold">{{ number_format($item->monto_original, 2, ',', '.') }}</td>
                            <td class="font-semibold">{{ number_format($item->saldo, 2, ',', '.') }}</td>
                            <td>
                                @if($item->estatus === 'P')
                                    <span class="badge-warning">Pendiente</span>
                                @elseif($item->estatus === 'C')
                                    <span class="badge-success">Cancelada</span>
                                @elseif($item->estatus === 'X')
                                    <span class="badge-danger">Anulada</span>
                                @else
                                    <span class="badge-secondary">{{ $item->estatus }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay deudas registradas
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
