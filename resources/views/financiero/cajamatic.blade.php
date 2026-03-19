<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">CajaMatic</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Administración de cajas del condominio</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.cajamatic.disponibilidad') }}" class="btn-secondary">
                    <i class="fas fa-chart-pie mr-2"></i>Disponibilidad
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards - Balance per caja -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($cajas as $caja)
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">{{ $caja->nombre }}</div>
                <div class="w-10 h-10 {{ $caja->activo ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register {{ $caja->activo ? 'text-green-600' : 'text-red-600' }}"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($caja->saldo, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">{{ $caja->ubicacion }}</p>
        </div>
        @endforeach
    </div>

    <!-- Cajas Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-cash-register mr-2 text-burgundy-800"></i>Listado de Cajas
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Saldo</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                        <tr>
                            <td class="font-medium">{{ $caja->nombre }}</td>
                            <td>{{ $caja->ubicacion }}</td>
                            <td class="font-semibold">{{ number_format($caja->saldo, 2, ',', '.') }} Bs</td>
                            <td>
                                @if($caja->activo)
                                    <span class="badge-success">Activo</span>
                                @else
                                    <span class="badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-navy-800 hover:text-burgundy-800 transition" title="Depositar">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay cajas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $cajas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
