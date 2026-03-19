<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Informe Anual</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Resumen anual de gestion del condominio</p>
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
            <form method="GET" action="{{ route('servicios.informes.informe-anual') }}">
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
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Generar Informe
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($edificio && !empty($data))
        <!-- Printable Area -->
        <div id="printable-area">
            <!-- Header del Informe -->
            <div class="card mb-6">
                <div class="card-body text-center py-6">
                    <h3 class="text-xl font-heading font-bold text-navy-800">
                        {{ $edificio->compania->nombre ?? 'Administradora' }}
                    </h3>
                    <p class="text-sm text-slate_custom-500">RIF: {{ $edificio->compania->rif ?? 'N/A' }}</p>
                    <p class="text-lg font-heading font-semibold text-burgundy-800 mt-2">
                        Informe Anual de Gestion - {{ $anio }}
                    </p>
                    <p class="text-sm text-slate_custom-500">Edificio: {{ $edificio->nombre }}</p>
                </div>
            </div>

            <!-- Resumen General -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Total Apartamentos</p>
                            <p class="text-2xl font-heading font-bold text-navy-800">{{ $data['total_aptos'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-building text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Gastos Anuales Est.</p>
                            <p class="text-2xl font-heading font-bold text-burgundy-800">{{ number_format($data['total_gastos_anual'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-burgundy-800/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-burgundy-800"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Cobrado en {{ $anio }}</p>
                            <p class="text-2xl font-heading font-bold text-green-600">{{ number_format($data['deudas_cobradas'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider">Deuda Pendiente</p>
                            <p class="text-2xl font-heading font-bold text-red-600">{{ number_format($data['deudas_pendientes'], 2, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos por Mes -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-chart-bar mr-2 text-burgundy-800"></i>Ingresos por Mes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-right">Ingresos Recibidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                    $totalIngresos = 0;
                                @endphp
                                @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $ingreso = $data['ingresos_por_mes'][$m] ?? 0;
                                        $totalIngresos += $ingreso;
                                    @endphp
                                    <tr>
                                        <td class="font-medium">{{ $meses[$m - 1] }}</td>
                                        <td class="text-right {{ $ingreso > 0 ? 'text-green-600' : 'text-slate_custom-400' }} font-semibold">
                                            {{ number_format($ingreso, 2, ',', '.') }} Bs
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50 font-bold">
                                    <td>Total Anual</td>
                                    <td class="text-right text-navy-800">{{ number_format($totalIngresos, 2, ',', '.') }} Bs</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detalle de Gastos -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-file-invoice mr-2 text-burgundy-800"></i>Estructura de Gastos Mensuales
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
                                    <th class="text-right">Monto Anual</th>
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
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50 font-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-right">{{ number_format($data['total_gastos_mensual'], 2, ',', '.') }} Bs</td>
                                    <td class="text-right">{{ number_format($data['total_gastos_anual'], 2, ',', '.') }} Bs</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Indicadores -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-tachometer-alt mr-2 text-burgundy-800"></i>Indicadores de Gestion
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-slate_custom-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-2">Morosos (2+ meses)</p>
                            <p class="text-3xl font-heading font-bold text-red-600">{{ $data['morosos_count'] }}</p>
                            <p class="text-xs text-slate_custom-400 mt-1">de {{ $data['total_aptos'] }} apartamentos</p>
                        </div>
                        <div class="bg-slate_custom-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-2">Tasa de Morosidad</p>
                            <p class="text-3xl font-heading font-bold {{ $data['total_aptos'] > 0 && ($data['morosos_count'] / $data['total_aptos'] * 100) > 30 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $data['total_aptos'] > 0 ? number_format($data['morosos_count'] / $data['total_aptos'] * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="bg-slate_custom-50 rounded-lg p-4 text-center">
                            <p class="text-xs text-slate_custom-400 uppercase tracking-wider mb-2">Eficiencia de Cobro</p>
                            @php
                                $totalFacturado = $data['deudas_cobradas'] + $data['deudas_pendientes'];
                                $eficiencia = $totalFacturado > 0 ? ($data['deudas_cobradas'] / $totalFacturado * 100) : 0;
                            @endphp
                            <p class="text-3xl font-heading font-bold {{ $eficiencia >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ number_format($eficiencia, 1) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="flex justify-end">
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print mr-2"></i>Imprimir Informe Anual
            </button>
        </div>
    @elseif(request()->has('edificio_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-folder-open text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No se encontraron datos para el edificio y ano seleccionados.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">Seleccione un edificio y ano para generar el informe anual.</p>
            </div>
        </div>
    @endif
</x-app-layout>
