<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    Listado de Morosos
                    <span class="inline-flex items-center justify-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700 ml-2">{{ $totalMorosos }}</span>
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">Apartamentos con 2 o mas meses de deuda pendiente</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.cobranza.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Cobranza
                </a>
                <a href="{{ route('financiero.cobranza.judicial') }}" class="btn-secondary">
                    <i class="fas fa-gavel mr-2"></i>Gestion Judicial
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Morosos</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-slash text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ $totalMorosos }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Apartamentos con 2+ meses de mora</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Adeudado</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-burgundy-800">{{ number_format($totalAdeudado, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Monto total en mora</p>
        </div>
    </div>

    <!-- Morosos Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>Propietarios Morosos
            </h3>
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
                            <th>Total Deuda</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($morosos as $moroso)
                        <tr>
                            <td>{{ $moroso->apartamento?->edificio?->nombre ?? 'N/A' }}</td>
                            <td class="font-medium">{{ $moroso->apartamento?->num_apto ?? 'N/A' }}</td>
                            <td>{{ $moroso->apartamento?->propietario_nombre ?? 'Sin asignar' }}</td>
                            <td>
                                @if($moroso->meses_vencidos >= 3)
                                    <span class="badge-danger">{{ $moroso->meses_vencidos }} meses</span>
                                @else
                                    <span class="badge-warning">{{ $moroso->meses_vencidos }} meses</span>
                                @endif
                            </td>
                            <td class="font-semibold text-red-600">{{ number_format($moroso->total_deuda, 2, ',', '.') }} Bs</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Enviar notificacion">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-check-circle text-3xl mb-2 block text-green-500"></i>
                                No hay propietarios morosos actualmente
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
