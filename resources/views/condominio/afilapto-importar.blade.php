<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Afiliaciones de Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga masiva de afiliaciones desde archivo CSV (afilapto)</p>
            </div>
            <a href="{{ route('condominio.afilapto.index') }}" class="btn-secondary">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Importados</div><div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div></div><div class="stat-value text-green-600">{{ $results['imported'] }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Actualizados</div><div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-sync-alt text-blue-600"></i></div></div><div class="stat-value text-blue-600">{{ $results['updated'] }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Omitidos</div><div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center"><i class="fas fa-forward text-yellow-600"></i></div></div><div class="stat-value text-yellow-600">{{ $results['skipped'] }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Errores</div><div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div></div><div class="stat-value text-red-600">{{ count($results['errors']) }}</div></div>
        </div>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            Importacion completada. {{ $results['imported'] }} nuevos, {{ $results['updated'] }} actualizados, {{ $results['skipped'] }} omitidos.
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
                <div class="overflow-x-auto"><table class="table-custom"><thead><tr><th>Linea</th><th>Ref</th><th>Razon</th></tr></thead><tbody>
                    @foreach($results['errors'] as $err)
                    <tr><td>{{ $err['line'] }}</td><td class="font-medium">{{ $err['ref'] ?: '--' }}</td><td class="text-red-600 text-xs">{{ $err['reason'] }}</td></tr>
                    @endforeach
                </tbody></table></div>
            </div>
        </div>
        @endif
        <div class="flex gap-3">
            <a href="{{ route('condominio.afilapto.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Importar Otro</a>
            <a href="{{ route('condominio.afilapto.index') }}" class="btn-primary"><i class="fas fa-list mr-2"></i>Ver Listado</a>
        </div>
    </div>

    {{-- ==================== PREVIEW ==================== --}}
    @elseif(isset($summary))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card"><div class="stat-label">Total Filas</div><div class="stat-value">{{ $summary['total'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Nuevos</div><div class="stat-value text-green-600">{{ $summary['new'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Existentes</div><div class="stat-value text-blue-600">{{ $summary['update'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Errores</div><div class="stat-value text-red-600">{{ $summary['error'] }}</div></div>
            <div class="stat-card"><div class="stat-label">Advertencias</div><div class="stat-value text-amber-600">{{ $summary['warnings'] }}</div></div>
        </div>

        @if($summary['warnings'] > 0)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $summary['warnings'] }} advertencias: edificios o apartamentos no encontrados en la BD. Los registros se importaran de igual forma con esos campos vacios.</span>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Vista Previa ({{ min(count($rows), 50) }} de {{ count($rows) }})
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>Estado</th><th>ID</th><th>Cod Afil</th><th>Edificio</th><th>Apto</th><th>Comp</th><th>Estatus</th><th>Notas</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($rows, 0, 50) as $row)
                            <tr class="{{ $row['status'] === 'error' ? 'bg-red-50' : (count($row['warnings'] ?? []) > 0 ? 'bg-amber-50' : '') }}">
                                <td class="text-xs">{{ $row['line'] }}</td>
                                <td>
                                    @if($row['status'] === 'new')<span class="badge-success text-xs">Nuevo</span>
                                    @elseif($row['status'] === 'update')<span class="badge-info text-xs">Existente</span>
                                    @else<span class="badge-danger text-xs">Error</span>@endif
                                </td>
                                <td class="text-xs">{{ $row['display']['legacy_id'] ?? '--' }}</td>
                                <td class="text-xs font-mono">{{ \Illuminate\Support\Str::limit($row['display']['cod_afil'] ?? '', 12) }}</td>
                                <td class="text-xs">{{ $row['display']['cod_edif'] ?? '--' }}</td>
                                <td class="font-medium text-sm">{{ $row['display']['num_apto'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['compania'] ?? '--' }}</td>
                                <td class="text-xs">
                                    @if(($row['display']['estatus'] ?? '') === 'A')<span class="badge-success text-xs">A</span>
                                    @elseif(($row['display']['estatus'] ?? '') === 'D')<span class="badge-danger text-xs">D</span>
                                    @else{{ $row['display']['estatus'] ?? '--' }}@endif
                                </td>
                                <td class="text-xs text-amber-600">{{ implode('; ', $row['warnings'] ?? []) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($summary['new'] > 0 || $summary['update'] > 0)
        <form action="{{ route('condominio.afilapto.importar.execute') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-cog mr-2 text-burgundy-800"></i>Opciones</h3></div>
                <div class="card-body">
                    @if($summary['update'] > 0)
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-navy-800 mb-3">{{ $summary['update'] }} registros ya existen:</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="update" checked class="text-burgundy-800 focus:ring-burgundy-800">
                                <div><p class="text-sm font-semibold text-navy-800">Actualizar</p><p class="text-xs text-slate_custom-400">Sobreescribir datos</p></div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="skip" class="text-burgundy-800 focus:ring-burgundy-800">
                                <div><p class="text-sm font-semibold text-navy-800">Omitir</p><p class="text-xs text-slate_custom-400">Solo importar nuevos</p></div>
                            </label>
                        </div>
                    </div>
                    @else<input type="hidden" name="duplicate_action" value="skip">@endif
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate_custom-200">
                        <a href="{{ route('condominio.afilapto.importar') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                        <button type="submit" class="btn-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Importando...'; this.form.submit();">
                            <i class="fas fa-upload mr-2"></i>Ejecutar ({{ $summary['new'] + $summary['update'] }} filas)
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>No hay filas validas. Revise el formato del archivo.
        </div>
        @endif
    </div>

    {{-- ==================== UPLOAD ==================== --}}
    @else
    <div class="card">
        <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo afilapto.csv</h3></div>
        <div class="card-body">
            <form action="{{ route('condominio.afilapto.importar.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="max-w-xl mx-auto text-center">
                    <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-file-upload text-2xl text-burgundy-800"></i></div>
                    <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Seleccione el archivo de afiliaciones</h4>
                    <p class="text-sm text-slate_custom-400 mb-6">CSV con comillas dobles. Max 50MB. Edificios/aptos no encontrados se importan con FK vacio.</p>
                    <div class="mb-6">
                        <input type="file" name="archivo" accept=".csv,.txt,.dat" required class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                        @error('archivo')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary"><i class="fas fa-eye mr-2"></i>Vista Previa</button>
                </div>
            </form>
            <div class="mt-8 pt-6 border-t border-slate_custom-200">
                <h5 class="text-sm font-heading font-semibold text-navy-800 mb-3"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Formato: 8 columnas CSV</h5>
                <div class="bg-slate_custom-50 rounded-xl p-4"><code class="text-xs text-navy-800 break-all">"ID","COD_AFIL","COD_EDIF","NUM_APTO","COMPANIA","ESTATUS","FECHA","OBS"</code></div>
                <div class="mt-3 flex items-start gap-2 text-xs text-slate_custom-500">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <span>Edificios/apartamentos no encontrados generan advertencia pero <strong>no bloquean</strong> la importacion.</span>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
