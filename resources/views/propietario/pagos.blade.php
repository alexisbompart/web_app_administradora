<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Mis Pagos Realizados</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-user mr-1"></i>{{ $propietario->nombres }} {{ $propietario->apellidos }} - CI: {{ $propietario->cedula }}
                </p>
            </div>
            <a href="{{ route('mi-condominio.dashboard') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    {{-- Filter Bar --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('mi-condominio.pagos') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate_custom-500 uppercase mb-1">Apartamento</label>
                    <select name="apartamento_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        <option value="">Todos los apartamentos</option>
                        @foreach($apartamentos as $apto)
                            <option value="{{ $apto->id }}" {{ request('apartamento_id') == $apto->id ? 'selected' : '' }}>
                                {{ $apto->edificio->nombre }} - Apto {{ $apto->num_apto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-receipt mr-2 text-burgundy-800"></i>Historial de Pagos
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Fecha Pago</th>
                            <th>Nro Recibo</th>
                            <th>Apartamento</th>
                            <th>Periodo</th>
                            <th>Forma de Pago</th>
                            <th>Referencia</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($pago->pago->fecha_pago)->format('d/m/Y') }}</td>
                                <td class="font-medium text-navy-800">{{ $pago->pago->numero_recibo ?? '--' }}</td>
                                <td>
                                    {{ $pago->apartamento->edificio->nombre }} - {{ $pago->apartamento->num_apto }}
                                </td>
                                <td>{{ $pago->periodo }}</td>
                                <td>
                                    <span class="badge-info">{{ $pago->pago->forma_pago ?? '--' }}</span>
                                </td>
                                <td class="text-sm text-slate_custom-500">{{ $pago->pago->numero_referencia ?? '--' }}</td>
                                <td class="font-semibold text-green-600">
                                    {{ number_format($pago->monto_aplicado, 2, ',', '.') }} Bs
                                </td>
                                <td>
                                    <a href="{{ route('mi-condominio.recibo', $pago->id) }}"
                                       class="btn-primary text-xs px-3 py-1.5">
                                        <i class="fas fa-file-pdf mr-1"></i>Ver Recibo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-slate_custom-400 py-8">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No hay pagos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pagos->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
