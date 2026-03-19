<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Plan Operativo</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Planificacion operativa del periodo</p>
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
                <i class="fas fa-filter mr-2 text-burgundy-800"></i>Seleccion de Edificio
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.informes.plan-operativo') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Edificio</label>
                        <select name="edificio_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">-- Seleccione Edificio --</option>
                            @foreach($edificios as $edif)
                                <option value="{{ $edif->id }}" {{ request('edificio_id') == $edif->id ? 'selected' : '' }}>
                                    {{ $edif->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Ano</label>
                        <select name="anio" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            @for($y = now()->year; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Consultar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($edificio && !empty($data))
        <div id="printable-area">
            <!-- Header -->
            <div class="card mb-6">
                <div class="card-body text-center py-6">
                    <h3 class="text-xl font-heading font-bold text-navy-800">
                        {{ $edificio->compania->nombre ?? 'Administradora' }}
                    </h3>
                    <p class="text-sm text-slate_custom-500">RIF: {{ $edificio->compania->rif ?? 'N/A' }}</p>
                    <p class="text-lg font-heading font-semibold text-burgundy-800 mt-2">
                        Plan Operativo {{ $anio }}
                    </p>
                    <p class="text-sm text-slate_custom-500">Edificio: {{ $edificio->nombre }}</p>
                </div>
            </div>

            <!-- Resumen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Apartamentos</p>
                            <p class="text-2xl font-heading font-bold text-navy-800">{{ $data['total_aptos'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-door-open text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Gasto Mensual</p>
                            <p class="text-2xl font-heading font-bold text-burgundy-800">{{ number_format($data['total_gastos_mensual'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-burgundy-800/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calculator text-burgundy-800"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Cuota Estimada</p>
                            <p class="text-2xl font-heading font-bold text-green-600">{{ number_format($data['cuota_estimada'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-coins text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Deuda Acumulada</p>
                            <p class="text-2xl font-heading font-bold text-red-600">{{ number_format($data['deudas_pendientes'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Presupuesto de Gastos -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Presupuesto de Gastos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Tipo</th>
                                    <th class="text-right">Monto Mensual</th>
                                    <th class="text-right">Presupuesto Anual</th>
                                    <th class="text-right">% del Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['gastos'] as $gasto)
                                <tr>
                                    <td class="font-medium">{{ $gasto->descripcion }}</td>
                                    <td>
                                        @if($gasto->tipo === 'fijo')
                                            <span class="badge-info">Fijo</span>
                                        @elseif($gasto->tipo === 'variable')
                                            <span class="badge-warning">Variable</span>
                                        @else
                                            <span class="badge-danger">{{ ucfirst($gasto->tipo) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-semibold">{{ number_format($gasto->monto_base, 2, ',', '.') }} Bs</td>
                                    <td class="text-right font-semibold">{{ number_format($gasto->monto_base * 12, 2, ',', '.') }} Bs</td>
                                    <td class="text-right">
                                        {{ $data['total_gastos_mensual'] > 0 ? number_format(($gasto->monto_base / $data['total_gastos_mensual']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50 font-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-right">{{ number_format($data['total_gastos_mensual'], 2, ',', '.') }} Bs</td>
                                    <td class="text-right">{{ number_format($data['total_gastos_anual'], 2, ',', '.') }} Bs</td>
                                    <td class="text-right">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Proyeccion de Cuotas -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-calculator mr-2 text-burgundy-800"></i>Proyeccion de Cuotas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-medium">Total Gastos Mensuales</td>
                                    <td class="text-right font-semibold">{{ number_format($data['total_gastos_mensual'], 2, ',', '.') }} Bs</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Cantidad de Apartamentos</td>
                                    <td class="text-right font-semibold">{{ $data['total_aptos'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Fondo de Reserva ({{ number_format($edificio->fondo_reserva_porcentaje ?? 10, 2) }}%)</td>
                                    <td class="text-right font-semibold">
                                        {{ number_format($data['cuota_estimada'] * (($edificio->fondo_reserva_porcentaje ?? 10) / 100), 2, ',', '.') }} Bs
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50 font-bold">
                                    <td>Cuota Estimada por Apartamento</td>
                                    <td class="text-right text-burgundy-800 text-lg">
                                        {{ number_format($data['cuota_estimada'] * (1 + (($edificio->fondo_reserva_porcentaje ?? 10) / 100)), 2, ',', '.') }} Bs
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="flex justify-end">
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print mr-2"></i>Imprimir Plan Operativo
            </button>
        </div>
    @elseif(request()->has('edificio_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-folder-open text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No se encontraron datos para el edificio seleccionado.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">Seleccione un edificio para generar el plan operativo.</p>
            </div>
        </div>
    @endif
</x-app-layout>
