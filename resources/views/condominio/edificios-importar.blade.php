<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Edificios</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga masiva de edificios desde archivo pipe-delimited</p>
            </div>
            <a href="{{ route('condominio.edificios.index') }}" class="btn-secondary">
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
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Importados</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ $results['imported'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Actualizados</div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-sync-alt text-blue-600"></i></div>
                </div>
                <div class="stat-value text-blue-600">{{ $results['updated'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Omitidos</div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center"><i class="fas fa-forward text-yellow-600"></i></div>
                </div>
                <div class="stat-value text-yellow-600">{{ $results['skipped'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Errores</div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div>
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
                        <thead><tr><th>Linea</th><th>Cod Edif</th><th>Razon</th></tr></thead>
                        <tbody>
                            @foreach($results['errors'] as $err)
                            <tr>
                                <td>{{ $err['line'] }}</td>
                                <td class="font-medium">{{ $err['cod_edif'] ?: '--' }}</td>
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
            <a href="{{ route('condominio.edificios.index') }}" class="btn-primary"><i class="fas fa-list mr-2"></i>Ver Listado de Edificios</a>
            <a href="{{ route('condominio.edificios.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Importar Otro Archivo</a>
        </div>
    </div>

    {{-- ==================== PREVIEW ==================== --}}
    @elseif(isset($summary))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Total Filas</div>
                    <div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-slate_custom-500"></i></div>
                </div>
                <div class="stat-value">{{ $summary['total'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Nuevos</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-plus-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ $summary['new'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Ya Existentes</div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-sync-alt text-blue-600"></i></div>
                </div>
                <div class="stat-value text-blue-600">{{ $summary['update'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Con Errores</div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div>
                </div>
                <div class="stat-value text-red-600">{{ $summary['error'] }}</div>
            </div>
        </div>

        @if(count($errors) > 0)
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Filas con errores ({{ count($errors) }})</span>
                    <i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="show" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>Info</th><th>Razon</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($errors, 0, 50) as $err)
                            <tr><td>{{ $err['line'] }}</td><td class="text-xs font-medium">{{ $err['info'] }}</td><td class="text-red-600 text-xs">{{ $err['reason'] }}</td></tr>
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
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Vista Previa (primeras {{ count($previewRows) }})
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Linea</th>
                                <th>Estado</th>
                                <th>COD_EDIF</th>
                                <th>Nombre</th>
                                <th>Compania</th>
                                <th>Ciudad</th>
                                <th>Aptos</th>
                                <th>RIF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewRows as $row)
                            <tr>
                                <td class="text-xs">{{ $row['line'] }}</td>
                                <td>
                                    @if($row['status'] === 'new')
                                        <span class="badge-success text-xs">Nuevo</span>
                                    @else
                                        <span class="badge-info text-xs">Existente</span>
                                    @endif
                                </td>
                                <td class="font-medium text-sm">{{ $row['display']['cod_edif'] ?? '--' }}</td>
                                <td class="text-xs">{{ \Illuminate\Support\Str::limit($row['display']['nombre'] ?? '', 30) }}</td>
                                <td class="text-xs">{{ $row['display']['compania'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['ciudad'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['cant_apto'] ?? '--' }}</td>
                                <td class="text-xs">{{ $row['display']['rif'] ?? '--' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($summary['new'] > 0 || $summary['update'] > 0)
        <form action="{{ route('condominio.edificios.importar.execute') }}" method="POST">
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
                        <p class="text-sm font-semibold text-navy-800 mb-3">Se encontraron {{ $summary['update'] }} edificios existentes. Que desea hacer?</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="update" checked class="text-burgundy-800 focus:ring-burgundy-800">
                                <div>
                                    <p class="text-sm font-semibold text-navy-800">Actualizar existentes</p>
                                    <p class="text-xs text-slate_custom-400">Sobreescribir los datos de los edificios que ya existen</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate_custom-200 rounded-xl cursor-pointer hover:bg-slate_custom-50 transition">
                                <input type="radio" name="duplicate_action" value="skip" class="text-burgundy-800 focus:ring-burgundy-800">
                                <div>
                                    <p class="text-sm font-semibold text-navy-800">Omitir existentes</p>
                                    <p class="text-xs text-slate_custom-400">Solo importar los edificios nuevos</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="duplicate_action" value="skip">
                    @endif
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate_custom-200">
                        <a href="{{ route('condominio.edificios.importar') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                        <button type="submit" class="btn-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Importando...'; this.form.submit();">
                            <i class="fas fa-upload mr-2"></i>Ejecutar Importacion ({{ $summary['new'] + $summary['update'] }} filas)
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>No hay filas validas para importar.
        </div>
        <a href="{{ route('condominio.edificios.importar') }}" class="btn-secondary mt-4"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        @endif
    </div>

    {{-- ==================== UPLOAD (default) ==================== --}}
    @else
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('condominio.edificios.importar.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="max-w-xl mx-auto text-center">
                    <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-city text-2xl text-burgundy-800"></i>
                    </div>
                    <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Seleccione el archivo de edificios</h4>
                    <p class="text-sm text-slate_custom-400 mb-6">Formato pipe-delimited (<code>|</code>) exportado desde MySQL. Maximo 10MB.</p>
                    <div class="mb-6">
                        <input type="file" name="archivo" accept=".csv,.txt,.dat" required
                               class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                        @error('archivo')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary"><i class="fas fa-eye mr-2"></i>Vista Previa</button>
                </div>
            </form>
            <div class="mt-8 pt-6 border-t border-slate_custom-200">
                <h5 class="text-sm font-heading font-semibold text-navy-800 mb-3">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>Formato esperado
                </h5>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <span><strong>COMPANIA</strong> (codigo) debe existir en la tabla de companias</span>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <span><strong>COD_EDIF</strong> es obligatorio y sirve como identificador unico</span>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <span>Duplicados se detectan por <strong>COD_EDIF</strong></span>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                        <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                        <span>Puede elegir actualizar u omitir existentes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
