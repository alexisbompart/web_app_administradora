<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Afiliaciones de Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga completa — reemplaza todas las afiliaciones existentes</p>
            </div>
            <a href="{{ route('condominio.afiliaciones-apto.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
            </a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- ==================== RESULTS ==================== --}}
    @if(isset($results))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Nuevos Insertados</div><div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-plus-circle text-green-600"></i></div></div><div class="stat-value text-green-600">{{ number_format($results['inserted']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Actualizados</div><div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-sync-alt text-blue-600"></i></div></div><div class="stat-value text-blue-600">{{ number_format($results['updated']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Errores</div><div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div></div><div class="stat-value text-red-600">{{ count($results['errors']) }}</div></div>
        </div>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>Importacion completada. {{ number_format($results['inserted']) }} nuevos insertados, {{ number_format($results['updated']) }} actualizados. Los registros existentes no incluidos en el archivo <strong class="ml-1">no fueron eliminados</strong>.
        </div>
        @if(count($results['errors']) > 0)
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Errores ({{ count($results['errors']) }})</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto"><table class="table-custom"><thead><tr><th>Info</th><th>Razon</th></tr></thead><tbody>
                    @foreach(array_slice($results['errors'], 0, 100) as $err)
                    <tr><td class="text-xs font-medium">{{ $err['info'] }}</td><td class="text-red-600 text-xs">{{ \Illuminate\Support\Str::limit($err['reason'], 100) }}</td></tr>
                    @endforeach
                </tbody></table></div>
            </div>
        </div>
        @endif
        <div class="flex gap-3">
            <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Nueva Carga</a>
            <a href="{{ route('condominio.afiliaciones-apto.index') }}" class="btn-primary"><i class="fas fa-list mr-2"></i>Ver Listado</a>
        </div>
    </div>

    {{-- ==================== PREVIEW ==================== --}}
    @elseif(isset($summary))
    <div class="space-y-6">

        <div class="bg-blue-50 border border-blue-300 text-blue-800 px-5 py-4 rounded-xl">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-xl mt-0.5"></i>
                <div>
                    <p class="font-heading font-bold">Importación sin borrado — solo inserta y actualiza</p>
                    <p class="text-sm mt-1">Se insertarán <strong>{{ number_format($summary['validas']) }}</strong> registros (nuevos o actualizando existentes por ID). Los registros que no vienen en el archivo <strong>no serán eliminados</strong>. <strong>{{ number_format($summary['omitidos']) }}</strong> filas omitidas por no tener edificio/apto en BD.</p>
                </div>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between"><div class="stat-label">Total Archivo</div><div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-slate_custom-500"></i></div></div>
                <div class="stat-value">{{ number_format($summary['total_archivo']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between"><div class="stat-label">Se Importarán</div><div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div></div>
                <div class="stat-value text-green-600">{{ number_format($summary['validas']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between"><div class="stat-label">Omitidos</div><div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-forward text-amber-600"></i></div></div>
                <div class="stat-value text-amber-600">{{ number_format($summary['omitidos']) }}</div>
                <p class="text-xs text-amber-500 mt-1">Sin edif/apto en BD</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between"><div class="stat-label">Errores Formato</div><div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div></div>
                <div class="stat-value text-red-600">{{ number_format($summary['errores']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between"><div class="stat-label">Actuales en BD</div><div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-slate_custom-500"></i></div></div>
                <div class="stat-value text-slate_custom-500">{{ number_format($summary['total_actual_bd']) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Se conservan</p>
            </div>
        </div>

        {{-- Resumen de razones de omisión --}}
        @if($summary['omitidos'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
            <p class="text-sm font-semibold text-amber-800 mb-3"><i class="fas fa-info-circle mr-2"></i>Motivos de omisión:</p>
            <div class="space-y-1">
                @foreach($summary['omitidos_por_razon'] as $razon => $cantidad)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-amber-700">{{ $razon }}</span>
                    <span class="font-semibold text-amber-900 bg-amber-200 px-2 py-0.5 rounded-full text-xs">{{ number_format($cantidad) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Detalle de omitidos --}}
        @if(!empty($omitidos))
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer bg-amber-50" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-amber-700 flex items-center justify-between w-full">
                    <span><i class="fas fa-forward mr-2"></i>Detalle de omitidos ({{ count($omitidos) }}{{ count($omitidos) >= 500 ? '+' : '' }}) — estas filas NO se importarán</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>Cod PINT</th><th>COD_EDIF</th><th>Num Apto</th><th>Motivo</th></tr></thead>
                        <tbody>
                            @foreach($omitidos as $om)
                            <tr class="bg-amber-50">
                                <td class="text-xs">{{ $om['line'] }}</td>
                                <td class="text-xs font-mono">{{ $om['cod_pint'] ?? '--' }}</td>
                                <td class="text-xs font-medium">{{ $om['cod_edif'] ?? '--' }}</td>
                                <td class="text-xs font-medium">{{ $om['num_apto'] ?? '--' }}</td>
                                <td class="text-xs text-amber-700 font-medium">{{ $om['reason'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Errores de formato --}}
        @if(!empty($errors))
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Errores de formato ({{ count($errors) }})</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="table-custom"><thead><tr><th>Linea</th><th>Info</th><th>Razon</th></tr></thead>
                    <tbody>@foreach(array_slice($errors, 0, 50) as $err)<tr><td>{{ $err['line'] }}</td><td class="text-xs font-medium">{{ $err['info'] }}</td><td class="text-red-600 text-xs">{{ $err['reason'] }}</td></tr>@endforeach</tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Vista previa de válidos --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Muestra de filas válidas (primeras {{ count($previewRows) }})
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>ID</th><th>PINT</th><th>Edificio</th><th>Apto</th><th>Comp</th><th>Estatus</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @foreach($previewRows as $row)
                            <tr>
                                <td class="text-xs">{{ $row['line'] }}</td>
                                <td class="text-xs">{{ $row['display']['legacy_id'] ?? '--' }}</td>
                                <td class="text-xs font-mono font-semibold">{{ $row['display']['cod_pint'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['cod_edif'] ?? '--' }}</td>
                                <td class="font-medium text-sm">{{ $row['display']['num_apto'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['compania'] ?? '--' }}</td>
                                <td class="text-xs">
                                    @if(($row['display']['estatus'] ?? '') === 'A')<span class="badge-success text-xs">A</span>
                                    @elseif(($row['display']['estatus'] ?? '') === 'D')<span class="badge-danger text-xs">D</span>
                                    @else{{ $row['display']['estatus'] ?? '--' }}@endif
                                </td>
                                <td class="text-xs">{{ $row['display']['fecha'] ?? '--' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($summary['validas'] > 0)
        <form action="{{ route('condominio.afiliaciones-apto.importar.execute') }}" method="POST" onsubmit="return confirm('Se procesarán {{ number_format($summary['validas']) }} registros (insertar/actualizar). Los registros existentes no incluidos en el archivo no serán eliminados. Continuar?')">
            @csrf
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                <button type="submit" class="btn-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Procesando...'; this.form.submit();">
                    <i class="fas fa-upload mr-2"></i>Importar Afiliaciones ({{ number_format($summary['validas']) }})
                </button>
            </div>
        </form>
        @else
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-times-circle"></i>No hay filas válidas para importar. Primero importe los edificios y apartamentos correspondientes.
        </div>
        <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-secondary mt-4"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        @endif
    </div>

    {{-- ==================== UPLOAD ==================== --}}
    @else
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Afiliaciones Actuales</div><div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-database text-navy-800"></i></div></div><div class="stat-value">{{ number_format($totalActual ?? 0) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Ultima Carga</div><div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-burgundy-800"></i></div></div><div class="stat-value text-sm">{{ isset($ultimaCarga) && $ultimaCarga ? \Carbon\Carbon::parse($ultimaCarga)->format('d/m/Y H:i') : 'Nunca' }}</div></div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo</h3></div>
            <div class="card-body">
                <form action="{{ route('condominio.afiliaciones-apto.importar.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-xl mx-auto text-center">
                        <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-link text-2xl text-burgundy-800"></i></div>
                        <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Afiliaciones de Apartamentos (afilapto)</h4>
                        <p class="text-sm text-slate_custom-400 mb-2">Reemplaza <strong class="text-red-600">todas las afiliaciones actuales</strong> con las del archivo.</p>
                        <p class="text-sm text-slate_custom-400 mb-6">CSV con comillas dobles: <code>"ID","PINT","EDIF","APTO","CIA","ESTATUS","FECHA","OBS"</code></p>
                        <div class="mb-6">
                            <input type="file" name="archivo" accept=".csv,.txt,.dat" required class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                            @error('archivo')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-eye mr-2"></i>Vista Previa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
