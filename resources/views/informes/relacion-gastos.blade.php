<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Relaci&oacute;n de Gastos Mensual</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Detalle de gastos por edificio y periodo</p>
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
            <form method="GET" action="{{ route('servicios.informes.relacion-gastos') }}">
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
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Periodo (Mes/A&ntilde;o)</label>
                        <input type="month" name="periodo" value="{{ request('periodo', now()->format('Y-m')) }}" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800" required>
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

    @if($edificio && $gastos->count() > 0)
        <!-- Printable Gastos Breakdown -->
        <div class="card mb-6" id="printable-area">
            <div class="card-header">
                <div class="text-center">
                    <h3 class="text-lg font-heading font-bold text-navy-800">
                        {{ $edificio->compania->nombre ?? 'Administradora' }}
                    </h3>
                    <p class="text-sm text-slate_custom-500">RIF: {{ $edificio->compania->rif ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="card-body">
                <!-- Edificio Info Header -->
                <div class="bg-slate_custom-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <span class="text-xs text-slate_custom-400 block">Edificio</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $edificio->nombre }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate_custom-400 block">Direcci&oacute;n</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $edificio->direccion ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate_custom-400 block">Periodo</span>
                            <span class="text-sm font-semibold text-navy-800">{{ \Carbon\Carbon::parse($periodo . '-01')->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Gastos Table -->
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th>Tipo</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gastos as $gasto)
                            <tr>
                                <td class="font-medium">{{ $gasto->descripcion }}</td>
                                <td>
                                    @if($gasto->tipo === 'fijo')
                                        <span class="badge-info">Fijo</span>
                                    @elseif($gasto->tipo === 'variable')
                                        <span class="badge-warning">Variable</span>
                                    @elseif($gasto->tipo === 'extraordinario')
                                        <span class="badge-danger">Extraordinario</span>
                                    @else
                                        <span class="badge-info">{{ ucfirst($gasto->tipo) }}</span>
                                    @endif
                                </td>
                                <td class="text-right font-semibold">{{ number_format($gasto->monto_base, 2, ',', '.') }} Bs</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="mt-6 border-t border-slate_custom-200 pt-4">
                    <div class="flex flex-col items-end space-y-2">
                        <div class="flex items-center gap-8">
                            <span class="text-sm font-medium text-slate_custom-600">Total Gastos:</span>
                            <span class="text-lg font-heading font-bold text-navy-800">{{ number_format($gastos->sum('monto_base'), 2, ',', '.') }} Bs</span>
                        </div>
                        <div class="flex items-center gap-8">
                            <span class="text-sm font-medium text-slate_custom-600">Al&iacute;cuota Base:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ number_format($edificio->alicuota_base, 4) }}</span>
                        </div>
                        <div class="flex items-center gap-8">
                            <span class="text-sm font-medium text-slate_custom-600">Fondo Reserva:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ number_format($edificio->fondo_reserva_porcentaje, 2) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="flex justify-end">
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print mr-2"></i>Imprimir Relaci&oacute;n de Gastos
            </button>
        </div>
    @elseif(request()->has('edificio_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-folder-open text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No se encontraron gastos para el edificio y periodo seleccionados.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">Seleccione un edificio y periodo para consultar la relaci&oacute;n de gastos.</p>
            </div>
        </div>
    @endif
</x-app-layout>
