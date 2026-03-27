<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Tasas BCV</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga del archivo cond_tasa_bcv.csv</p>
            </div>
            <a href="{{ route('financiero.tasabcv.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-times-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- ESTADO 3: RESULTADOS --}}
    @if(isset($results))
    <div class="card mb-6">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-check-circle mr-2 text-green-600"></i>Importacion Completada</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="stat-card"><div class="stat-label">Insertados</div><div class="stat-value text-green-600">{{ $results['insertados'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Actualizados</div><div class="stat-value text-blue-600">{{ $results['actualizados'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Total Procesados</div><div class="stat-value text-navy-800">{{ $results['total_procesados'] }}</div></div>
            </div>
            <div class="mt-4">
                <a href="{{ route('financiero.tasabcv.index') }}" class="btn-primary"><i class="fas fa-list mr-2"></i>Ver Tasas</a>
            </div>
        </div>
    </div>

    {{-- ESTADO 2: PREVIEW --}}
    @elseif(isset($summary))
    <div class="card mb-6">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-search mr-2 text-burgundy-800"></i>Vista Previa</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                <div class="stat-card"><div class="stat-label">Lineas</div><div class="stat-value">{{ $summary['total_lineas'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Validas</div><div class="stat-value text-green-600">{{ $summary['validas'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Duplicadas</div><div class="stat-value text-amber-600">{{ $summary['duplicadas'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Errores</div><div class="stat-value text-red-600">{{ $summary['errores'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Desde</div><div class="stat-value text-sm">{{ $summary['fecha_min'] }}</div></div>
                <div class="stat-card"><div class="stat-label">Hasta</div><div class="stat-value text-sm">{{ $summary['fecha_max'] }}</div></div>
            </div>

            @if($summary['validas'] > 0)
            <form action="{{ route('financiero.tasabcv.importar.execute') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary"><i class="fas fa-upload mr-2"></i>Ejecutar Importacion ({{ $summary['validas'] }} tasas)</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Preview table --}}
    @if(isset($unique) && $unique->count())
    <div class="card mb-6">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-table mr-2 text-burgundy-800"></i>Muestra (primeras 50)</h3></div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead><tr><th>Fecha</th><th class="text-right">Tasa</th><th>ID Legacy</th></tr></thead>
                    <tbody>
                        @foreach($unique->take(50) as $row)
                        <tr>
                            <td>{{ $row['fecha'] }}</td>
                            <td class="text-right font-mono font-semibold">{{ number_format($row['tasa'], 4, ',', '.') }}</td>
                            <td class="text-xs text-slate_custom-400">{{ $row['legacy_id'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Errors --}}
    @if(!empty($errors))
    <div class="card">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Errores ({{ count($errors) }})</h3></div>
        <div class="card-body p-0">
            <div class="overflow-x-auto max-h-64">
                <table class="table-custom">
                    <thead><tr><th>Fila</th><th>Error</th></tr></thead>
                    <tbody>
                        @foreach(array_slice($errors, 0, 50) as $err)
                        <tr><td>{{ $err['fila'] }}</td><td class="text-red-600 text-xs">{{ $err['error'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ESTADO 1: UPLOAD --}}
    @else
    <div class="card">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-file-csv mr-2 text-burgundy-800"></i>Subir Archivo</h3></div>
        <div class="card-body">
            @if(isset($totalActual))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-700">
                <i class="fas fa-info-circle mr-1"></i>Actualmente hay <strong>{{ number_format($totalActual) }}</strong> tasas registradas.
                @if($ultimaCarga) Ultima actualizacion: {{ \Carbon\Carbon::parse($ultimaCarga)->format('d/m/Y H:i') }} @endif
            </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4 text-sm text-amber-700">
                <i class="fas fa-lightbulb mr-1"></i>Formato esperado: CSV con columnas <code>"ID","TASA","CREATED_AT","UPDATED_AT"</code> separadas por coma.
                Las fechas duplicadas se resuelven manteniendo el registro con ID mas alto.
            </div>

            <form action="{{ route('financiero.tasabcv.importar.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-navy-800 mb-1">Archivo CSV</label>
                        <input type="file" name="archivo" accept=".csv,.txt" required
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                    </div>
                    <button type="submit" class="btn-primary"><i class="fas fa-search mr-2"></i>Previsualizar</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-app-layout>
