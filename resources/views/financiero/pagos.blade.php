<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pagos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Registro de pagos realizados en el condominio</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('financiero.pagos.importar') }}" class="btn-secondary">
                    <i class="fas fa-file-import mr-2"></i>Importar Pagos
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
                    <i class="fas fa-money-check-alt text-navy-800"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Pagos registrados</p>
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
            <div class="stat-value text-sm">cond_pagos</div>
            <p class="text-xs text-slate_custom-400 mt-1">Fuente de datos</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-money-check-alt mr-2 text-burgundy-800"></i>Listado de Pagos
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID Pago Legacy</th>
                            <th>Cajero</th>
                            <th>Fecha Pago</th>
                            <th>Monto Total</th>
                            <th>Tipo Pago</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td class="font-medium text-navy-800">{{ $item->id_pago_legacy ?? 'N/A' }}</td>
                            <td>{{ $item->cajero ?? 'N/A' }}</td>
                            <td>{{ $item->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="font-semibold">{{ number_format($item->monto_total, 2, ',', '.') }}</td>
                            <td>{{ $item->tipo_pago ?? 'N/A' }}</td>
                            <td>
                                @if($item->estatus === 'A' || $item->estatus === 'Activo')
                                    <span class="badge-success">{{ $item->estatus }}</span>
                                @elseif($item->estatus === 'X' || $item->estatus === 'Anulado')
                                    <span class="badge-danger">{{ $item->estatus }}</span>
                                @else
                                    <span class="badge-warning">{{ $item->estatus ?? 'N/A' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay pagos registrados
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
