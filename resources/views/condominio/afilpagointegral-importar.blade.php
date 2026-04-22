<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Afiliaciones Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga masiva de afiliaciones pago integral desde archivo CSV</p>
            </div>
            <a href="{{ route('condominio.apartamentos.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    {{-- Alerts --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- ==================== RESULTS STATE ==================== --}}
    @if(isset($results))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Importados</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="stat-value text-green-600">{{ $results['imported'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Actualizados</div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-blue-600"></i>
                    </div>
                </div>
                <div class="stat-value text-blue-600">{{ $results['updated'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Omitidos</div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-forward text-yellow-600"></i>
                    </div>
                </div>
                <div class="stat-value text-yellow-600">{{ $results['skipped'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Errores</div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <div class="stat-value text-red-600">{{ count($results['errors']) }}</div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            Importacion completada. {{ $results['imported'] }} nuevos, {{ $results['updated'] }} actualizados, {{ $results['skipped'] }} omitidos.
        </div>

        @if(count($results['errors']) > 0)
        <div class="card" x-data="{ showErrors: false }">
            <div class="card-header cursor-pointer" @click="showErrors = !showErrors">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Detalle de Errores ({{ count($results['errors']) }})</span>
                    <i class="fas" :class="showErrors ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="showErrors" x-transition>
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Linea</th>
                                <th>Cedula</th>
                                <th>Razon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['errors'] as $err)
                            <tr>
                                <td>{{ $err['line'] }}</td>
                                <td class="font-medium">{{ $err['ref'] ?: '--' }}</td>
                                <td class="text-red-600 text-xs">{{ $err['reason'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('condominio.afilpagointegral.importar') }}" class="btn-secondary">
                <i class="fas fa-file-import mr-2"></i>Importar Otro Archivo
            </a>
            <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="btn-primary">
                <i class="fas fa-arrow-right mr-2"></i>Siguiente: Importar afilapto
            </a>
        </div>
    </div>

    {{-- ==================== PREVIEW STATE ==================== --}}
    @elseif(isset($summary))
    <div class="space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Total Archivo</div>
                    <div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-slate_custom-500"></i></div>
                </div>
                <div class="stat-value">{{ number_format($summary['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Se Importarán</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ number_format($summary['new'] + $summary['update']) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">{{ $summary['new'] }} nuevos + {{ $summary['update'] }} actualizados</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Omitidos</div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-forward text-amber-600"></i></div>
                </div>
                <div class="stat-value text-amber-600">{{ number_format($summary['omitidos']) }}</div>
                <p class="text-xs text-amber-500 mt-1">Sin edif/apto en afilapto</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Ya Existentes</div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-sync-alt text-blue-600"></i></div>
                </div>
                <div class="stat-value text-blue-600">{{ number_format($summary['update']) }}</div>
            </div>
        </div>

        {{-- Resumen motivos de omisión --}}
        @if($summary['omitidos'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">
            <p class="text-sm font-semibold text-amber-800 mb-3"><i class="fas fa-info-circle mr-2"></i>Motivos de omisión (registros que NO se importarán):</p>
            <div class="space-y-1">
                @foreach($summary['omitidos_por_razon'] as $razon => $cantidad)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-amber-700">{{ $razon }}</span>
                    <span class="font-semibold text-amber-900 bg-amber-200 px-2 py-0.5 rounded-full text-xs">{{ number_format($cantidad) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer bg-amber-50" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-amber-700 flex items-center justify-between w-full">
                    <span><i class="fas fa-forward mr-2"></i>Detalle de omitidos ({{ count($omitidos) }}{{ count($omitidos) >= 500 ? '+' : '' }}) — NO se importarán</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>Afil ID</th><th>Cédula</th><th>Nombres</th><th>Motivo</th></tr></thead>
                        <tbody>
                            @foreach($omitidos as $om)
                            <tr class="bg-amber-50">
                                <td class="text-xs">{{ $om['line'] }}</td>
                                <td class="text-xs font-medium">{{ $om['afilapto_id'] }}</td>
                                <td class="text-xs">{{ $om['cedula'] }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($om['nombres'] ?? '', 25) }}</td>
                                <td class="text-xs text-amber-700 font-medium">{{ $om['reason'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Vista Previa (primeras {{ min(count($rows), 50) }} de {{ count($rows) }} filas)
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Linea</th>
                                <th>Estado</th>
                                <th>Afil ID</th>
                                <th>Cedula</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($rows, 0, 50) as $row)
                            <tr class="{{ $row['status'] === 'error' ? 'bg-red-50' : '' }}">
                                <td class="text-xs">{{ $row['line'] }}</td>
                                <td>
                                    @if($row['status'] === 'new')
                                        <span class="badge-success text-xs">Nuevo</span>
                                    @elseif($row['status'] === 'update')
                                        <span class="badge-info text-xs">Existente</span>
                                    @else
                                        <span class="badge-danger text-xs" title="{{ implode(', ', $row['errors']) }}">Error</span>
                                    @endif
                                </td>
                                <td class="text-xs">{{ $row['display']['afilapto_id'] ?? '--' }}</td>
                                <td class="font-medium text-sm">{{ $row['display']['cedula'] ?? '--' }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($row['display']['nombres'] ?? '', 20) }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($row['display']['apellidos'] ?? '', 20) }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($row['display']['email'] ?? '', 25) }}</td>
                                <td class="text-xs">
                                    @if(($row['display']['estatus'] ?? '') === 'A')
                                        <span class="badge-success text-xs">A</span>
                                    @elseif(($row['display']['estatus'] ?? '') === 'D')
                                        <span class="badge-danger text-xs">D</span>
                                    @elseif(($row['display']['estatus'] ?? '') === 'T')
                                        <span class="badge-warning text-xs">T</span>
                                    @else
                                        {{ $row['display']['estatus'] ?? '--' }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($summary['new'] > 0 || $summary['update'] > 0)
        <form action="{{ route('condominio.afilpagointegral.importar.execute') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-cog mr-2 text-burgundy-800"></i>Opciones de Importacion
                    </h3>
                </div>
                <div class="card-body">
                    @if($summary['update'] > 0)
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-navy-800 mb-3">Se encontraron {{ $summary['update'] }} registros que ya existen. Que desea hacer?</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="update" checked class="text-burgundy-800 focus:ring-burgundy-800">
                                <div>
                                    <p class="text-sm font-semibold text-navy-800">Actualizar existentes</p>
                                    <p class="text-xs text-slate_custom-400">Sobreescribir los datos de los registros que ya existen</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="skip" class="text-burgundy-800 focus:ring-burgundy-800">
                                <div>
                                    <p class="text-sm font-semibold text-navy-800">Omitir existentes</p>
                                    <p class="text-xs text-slate_custom-400">Solo importar los registros nuevos</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="duplicate_action" value="skip">
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate_custom-200">
                        <a href="{{ route('condominio.afilpagointegral.importar') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Importando...'; this.form.submit();">
                            <i class="fas fa-upload mr-2"></i>Ejecutar Importacion ({{ $summary['new'] + $summary['update'] }} filas)
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl flex items-start gap-2">
            <i class="fas fa-exclamation-triangle mt-0.5"></i>
            <div>
                <p class="font-semibold">No hay filas validas para importar.</p>
                <ul class="mt-2 text-sm list-disc list-inside space-y-1">
                    <li>Revise que el archivo tenga al menos 28 columnas por fila</li>
                    <li>Verifique que el campo <strong>afilapto_id</strong> (columna 1) sea numerico</li>
                    <li>Asegurese de que el formato sea CSV con comillas dobles</li>
                </ul>
            </div>
        </div>
        <a href="{{ route('condominio.afilpagointegral.importar') }}" class="btn-secondary mt-4">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
        @endif
    </div>

    {{-- ==================== UPLOAD STATE (default) ==================== --}}
    @else
    <div class="space-y-6">
        {{-- Info about order --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            <span>Este archivo se importa <strong>antes</strong> de afilapto.csv. Despues de importar este archivo, importe
                <a href="{{ route('condominio.afiliaciones-apto.importar') }}" class="underline font-semibold">afilapto.csv</a>
                para completar la relacion.
            </span>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo afilpagointegral.csv
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('condominio.afilpagointegral.importar.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-xl mx-auto text-center">
                        <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-upload text-2xl text-burgundy-800"></i>
                        </div>
                        <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Seleccione el archivo de pago integral</h4>
                        <p class="text-sm text-slate_custom-400 mb-6">Formato CSV con campos entre comillas dobles. Maximo 50MB.</p>

                        <div class="mb-6">
                            <input type="file" name="archivo" accept=".csv,.txt,.dat" required
                                   class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                            @error('archivo')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-eye mr-2"></i>Vista Previa
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate_custom-200">
                    <h5 class="text-sm font-heading font-semibold text-navy-800 mb-3">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>Formato esperado del archivo
                    </h5>
                    <div class="bg-slate_custom-50 rounded-xl p-4 overflow-x-auto">
                        <p class="text-xs text-slate_custom-500 mb-2">Columnas principales (37 campos CSV):</p>
                        <code class="text-xs text-navy-800 break-all">afilapto_id, fecha, letra, cedula_rif, nombres, apellidos, email, email_alterno, calle_avenida, piso_apto, edif_casa, urbanizacion, ciudad, estado_id, telefono, fax, celular, otro, banco_id, cta_bancaria, tipo_cta, nom_usuario, clave, creado_por, cod_sucursal, estatus, fecha_estatus, observaciones, ...</code>
                        <p class="text-xs text-slate_custom-500 mt-3 mb-2">Ejemplo:</p>
                        <code class="text-xs text-navy-800 break-all">"1","2020-02-27","V","11899943","JOSE","PEREZ","email@test.com",NULL,...</code>
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span><strong>afilapto_id</strong> debe existir en la tabla afilapto</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Duplicados se detectan por <strong>afilapto_id</strong></span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Valores <strong>NULL</strong> se interpretan como campos vacios</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                            <span>Importe <strong>afilapto primero</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
