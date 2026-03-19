<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Gestión de pagos integrales del condominio</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.pago-integral.consultar-saldo') }}" class="btn-secondary">
                    <i class="fas fa-search-dollar mr-2"></i>Consultar Saldo
                </a>
                <a href="{{ route('financiero.pago-integral.procesar') }}" class="btn-primary"
                   onclick="event.preventDefault();">
                    <i class="fas fa-plus mr-2"></i>Nuevo Pago
                </a>
            </div>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-credit-card mr-2 text-burgundy-800"></i>Listado de Pagos Integrales
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                            <th>Forma Pago</th>
                            <th>Referencia</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td class="font-medium">#{{ $pago->id }}</td>
                            <td>{{ $pago->fecha?->format('d/m/Y') }}</td>
                            <td class="font-semibold">{{ number_format($pago->monto_total, 2, ',', '.') }} Bs</td>
                            <td>{{ ucfirst($pago->forma_pago) }}</td>
                            <td>{{ $pago->referencia }}</td>
                            <td>
                                @if($pago->estatus === 'P')
                                    <span class="badge-warning">Pendiente</span>
                                @elseif($pago->estatus === 'A')
                                    <span class="badge-success">Aprobado</span>
                                @elseif($pago->estatus === 'R')
                                    <span class="badge-danger">Rechazado</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('financiero.pago-integral.comprobante', $pago) }}" class="text-navy-800 hover:text-burgundy-800 transition" title="Ver comprobante">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay pagos integrales registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pagos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
