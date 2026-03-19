<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Estado de Cuenta</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Consulta de estado de cuenta por apartamento</p>
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
                <i class="fas fa-filter mr-2 text-burgundy-800"></i>Filtros de Consulta
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.informes.estado-cuenta') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Edificio</label>
                        <select name="edificio_id" id="edificio_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccione Edificio --</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ request('edificio_id') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Apartamento</label>
                        <select name="apartamento_id" id="apartamento_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccione Apartamento --</option>
                            @foreach($apartamentos as $apto)
                                <option value="{{ $apto->id }}" {{ request('apartamento_id') == $apto->id ? 'selected' : '' }}>
                                    {{ $apto->num_apto }} - {{ $apto->propietario_nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Periodo Desde</label>
                        <input type="month" name="periodo_desde" value="{{ request('periodo_desde') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Periodo Hasta</label>
                        <input type="month" name="periodo_hasta" value="{{ request('periodo_hasta') }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                </div>
                <div class="flex justify-end mt-4 gap-3">
                    <a href="{{ route('servicios.informes.estado-cuenta') }}" class="btn-secondary">
                        <i class="fas fa-eraser mr-2"></i>Limpiar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter apartments by edificio -->
    <script>
        document.getElementById('edificio_id').addEventListener('change', function() {
            const edificioId = this.value;
            const aptoSelect = document.getElementById('apartamento_id');
            const allOptions = @json($apartamentos->map(fn($a) => ['id' => $a->id, 'edificio_id' => $a->edificio_id, 'label' => $a->num_apto . ' - ' . $a->propietario_nombre]));

            aptoSelect.innerHTML = '<option value="">-- Seleccione Apartamento --</option>';
            allOptions.forEach(function(opt) {
                if (!edificioId || opt.edificio_id == edificioId) {
                    const option = document.createElement('option');
                    option.value = opt.id;
                    option.textContent = opt.label;
                    aptoSelect.appendChild(option);
                }
            });
        });
    </script>

    @if($apartamento)
        <!-- Propietario Info Card -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-user mr-2 text-burgundy-800"></i>Datos del Propietario
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Propietario</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->propietario_nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">C&eacute;dula</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->propietario_cedula ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Apartamento</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->num_apto }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Edificio</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->edificio->nombre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Deuda</p>
                        <p class="text-2xl font-heading font-bold text-red-600">{{ number_format($deudas->sum('monto_original') + $deudas->sum('monto_mora'), 2, ',', '.') }} Bs</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Pagado</p>
                        <p class="text-2xl font-heading font-bold text-green-600">{{ number_format($deudas->sum('monto_pagado'), 2, ',', '.') }} Bs</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Saldo Pendiente</p>
                        <p class="text-2xl font-heading font-bold text-burgundy-800">{{ number_format($deudas->sum('saldo'), 2, ',', '.') }} Bs</p>
                    </div>
                    <div class="w-12 h-12 bg-burgundy-800/10 rounded-xl flex items-center justify-center">
                        <i class="fas fa-balance-scale text-burgundy-800"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deudas Table -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-file-invoice mr-2 text-burgundy-800"></i>Deudas
                </h3>
            </div>
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Monto Original</th>
                                <th>Mora</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deudas as $deuda)
                            <tr>
                                <td class="font-medium">{{ $deuda->periodo }}</td>
                                <td>{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                <td>{{ number_format($deuda->monto_mora, 2, ',', '.') }} Bs</td>
                                <td>{{ number_format($deuda->monto_pagado, 2, ',', '.') }} Bs</td>
                                <td class="font-semibold">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                <td>
                                    @if($deuda->estatus === 'P')
                                        <span class="badge-warning">Pendiente</span>
                                    @elseif($deuda->estatus === 'V')
                                        <span class="badge-danger">Vencida</span>
                                    @elseif($deuda->estatus === 'C')
                                        <span class="badge-success">Cancelada</span>
                                    @else
                                        <span class="badge-info">{{ $deuda->estatus }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-slate_custom-400 py-8">
                                    <i class="fas fa-check-circle text-3xl mb-2 block text-green-400"></i>
                                    No tiene deudas registradas
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagos Table -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-money-check-alt mr-2 text-burgundy-800"></i>Pagos Realizados
                </h3>
            </div>
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Referencia</th>
                                <th>Forma de Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->condPago->fecha_pago?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="font-medium">{{ $pago->condPago->numero_referencia ?? 'N/A' }}</td>
                                <td>{{ ucfirst($pago->condPago->forma_pago ?? 'N/A') }}</td>
                                <td class="font-semibold">{{ number_format($pago->monto_aplicado, 2, ',', '.') }} Bs</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate_custom-400 py-8">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    No hay pagos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="flex justify-end">
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print mr-2"></i>Imprimir Estado de Cuenta
            </button>
        </div>
    @elseif(request()->has('apartamento_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-search text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No se encontraron resultados para los filtros seleccionados.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">Seleccione un edificio y apartamento para consultar el estado de cuenta.</p>
            </div>
        </div>
    @endif
</x-app-layout>
