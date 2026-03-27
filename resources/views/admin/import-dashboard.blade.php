<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Centro de Importaciones</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Estado de todas las tablas importadas desde el sistema legacy</p>
            </div>
        </div>
    </x-slot>

    {{-- Resumen General --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @php $totalRegistros = collect($tables)->sum('total'); @endphp
        <div class="stat-card col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <div class="stat-label">Total Registros</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-database text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-burgundy-800">{{ number_format($totalRegistros) }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">En {{ count($tables) }} tablas</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Tablas con Datos</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600">{{ collect($tables)->where('total', '>', 0)->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Tablas Vacias</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ collect($tables)->where('total', 0)->count() }}</div>
        </div>
    </div>

    {{-- Tabla de Estado --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-table mr-2 text-burgundy-800"></i>Estado de Importaciones
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tabla</th>
                            <th>Registros</th>
                            <th>Tipo Carga</th>
                            <th>Ultima Actualizacion</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $table)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-{{ $table['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="{{ $table['icono'] }} text-{{ $table['color'] }}-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-navy-800 text-sm">{{ $table['nombre'] }}</p>
                                        <p class="text-xs text-slate_custom-400">{{ $table['tabla'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-lg font-heading font-bold {{ $table['total'] > 0 ? 'text-navy-800' : 'text-slate_custom-300' }}">
                                    {{ number_format($table['total']) }}
                                </span>
                            </td>
                            <td>
                                @if($table['tipo'] === 'Carga Completa')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <i class="fas fa-sync-alt mr-1"></i>{{ $table['tipo'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <i class="fas fa-plus-circle mr-1"></i>{{ $table['tipo'] }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($table['ultima_carga'])
                                    <div>
                                        <p class="text-sm text-navy-800">{{ \Carbon\Carbon::parse($table['ultima_carga'])->format('d/m/Y') }}</p>
                                        <p class="text-xs text-slate_custom-400">{{ \Carbon\Carbon::parse($table['ultima_carga'])->format('H:i:s') }}</p>
                                    </div>
                                @else
                                    <span class="text-xs text-slate_custom-300">Nunca</span>
                                @endif
                            </td>
                            <td>
                                @if($table['total'] > 0)
                                    <span class="badge-success text-xs"><i class="fas fa-check mr-1"></i>Con datos</span>
                                @else
                                    <span class="badge-warning text-xs"><i class="fas fa-clock mr-1"></i>Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route($table['ruta_import']) }}" class="inline-flex items-center px-3 py-1.5 bg-burgundy-800 text-white text-xs font-semibold rounded-lg hover:bg-burgundy-700 transition">
                                        <i class="fas fa-file-import mr-1"></i>Importar
                                    </a>
                                    @if($table['ruta_listado'])
                                    <a href="{{ route($table['ruta_listado']) }}" class="btn-secondary text-xs px-2 py-1.5">
                                        <i class="fas fa-list mr-1"></i>Ver
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Orden de importación --}}
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-sort-numeric-down mr-2 text-burgundy-800"></i>Orden Recomendado de Importacion
            </h3>
        </div>
        <div class="card-body">
            <div class="flex flex-wrap gap-3">
                @php
                    $orden = [
                        ['num' => 1, 'nombre' => 'Edificios', 'icon' => 'fas fa-city'],
                        ['num' => 2, 'nombre' => 'Apartamentos', 'icon' => 'fas fa-door-open'],
                        ['num' => 3, 'nombre' => 'Afil. Pago Integral', 'icon' => 'fas fa-hand-holding-usd'],
                        ['num' => 4, 'nombre' => 'Afil. Apto', 'icon' => 'fas fa-link'],
                        ['num' => 5, 'nombre' => 'Gastos', 'icon' => 'fas fa-receipt'],
                        ['num' => 6, 'nombre' => 'Deudas', 'icon' => 'fas fa-file-invoice-dollar'],
                        ['num' => 7, 'nombre' => 'Descuentos', 'icon' => 'fas fa-percentage'],
                        ['num' => 8, 'nombre' => 'Abonos', 'icon' => 'fas fa-money-bill-wave'],
                        ['num' => 9, 'nombre' => 'Pagos', 'icon' => 'fas fa-money-check-alt'],
                        ['num' => 10, 'nombre' => 'Pagos x Apto', 'icon' => 'fas fa-credit-card'],
                        ['num' => 11, 'nombre' => 'Mov. Pre-fact', 'icon' => 'fas fa-exchange-alt'],
                        ['num' => 12, 'nombre' => 'Fact. x Apto', 'icon' => 'fas fa-file-invoice'],
                        ['num' => 13, 'nombre' => 'Fact. x Edificio', 'icon' => 'fas fa-building'],
                    ];
                @endphp
                @foreach($orden as $paso)
                <div class="flex items-center gap-2 px-3 py-2 bg-slate_custom-50 rounded-lg">
                    <span class="w-6 h-6 bg-burgundy-800 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $paso['num'] }}</span>
                    <i class="{{ $paso['icon'] }} text-slate_custom-500 text-xs"></i>
                    <span class="text-sm text-navy-800">{{ $paso['nombre'] }}</span>
                    @if($paso['num'] < count($orden))
                    <i class="fas fa-chevron-right text-slate_custom-300 text-xs ml-1"></i>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
