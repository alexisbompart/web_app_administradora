<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Deudas de Apartamentos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga completa diaria — reemplaza todas las deudas existentes</p>
            </div>
            <a href="{{ route('financiero.cobranza.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Cobranza
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
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Deudas Anteriores</div>
                    <div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-slate_custom-500"></i></div>
                </div>
                <div class="stat-value text-slate_custom-500">{{ number_format($results['previous_count']) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Eliminadas</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Deudas Importadas</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ number_format($results['imported']) }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Insertadas correctamente</p>
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
            Carga completa exitosa. Se eliminaron {{ number_format($results['previous_count']) }} deudas anteriores y se importaron {{ number_format($results['imported']) }} nuevas.
        </div>

        @if(count($results['errors']) > 0)
        <div class="card" x-data="{ showErrors: false }">
            <div class="card-header cursor-pointer" @click="showErrors = !showErrors">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Errores de insercion ({{ count($results['errors']) }})</span>
                    <i class="fas" :class="showErrors ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="showErrors" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Info</th><th>Razon</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($results['errors'], 0, 100) as $err)
                            <tr>
                                <td class="font-medium text-xs">{{ $err['info'] }}</td>
                                <td class="text-red-600 text-xs">{{ \Illuminate\Support\Str::limit($err['reason'], 100) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('financiero.cobranza.index') }}" class="btn-primary"><i class="fas fa-hand-holding-usd mr-2"></i>Ir a Cobranza</a>
            <a href="{{ route('financiero.deudas.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Nueva Carga</a>
        </div>
    </div>

    {{-- ==================== PREVIEW ==================== --}}
    @elseif(isset($summary))
    <div class="space-y-6">
        {{-- Warning banner --}}
        <div class="bg-amber-50 border border-amber-300 text-amber-800 px-5 py-4 rounded-xl">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-xl mt-0.5"></i>
                <div>
                    <p class="font-heading font-bold">Carga Completa — Se reemplazaran TODAS las deudas</p>
                    <p class="text-sm mt-1">Al ejecutar, se eliminaran las <strong>{{ number_format($summary['total_actual_bd']) }} deudas actuales</strong> de la base de datos y se insertaran las <strong>{{ number_format($summary['validas']) }} deudas validas</strong> del archivo. Esta accion no se puede deshacer.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Filas en Archivo</div>
                    <div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-slate_custom-500"></i></div>
                </div>
                <div class="stat-value">{{ number_format($summary['total_archivo']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Validas</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ number_format($summary['validas']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Con Errores</div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div>
                </div>
                <div class="stat-value text-red-600">{{ number_format($summary['errores']) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Actuales en BD</div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-amber-600"></i></div>
                </div>
                <div class="stat-value text-amber-600">{{ number_format($summary['total_actual_bd']) }}</div>
                <p class="text-xs text-red-500 mt-1">Seran eliminadas</p>
            </div>
        </div>

        {{-- Error details --}}
        @if(count($errors) > 0)
        <div class="card" x-data="{ showErrors: false }">
            <div class="card-header cursor-pointer" @click="showErrors = !showErrors">
                <h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full">
                    <span><i class="fas fa-exclamation-triangle mr-2"></i>Filas con errores que NO se importaran ({{ count($errors) }})</span>
                    <i class="fas" :class="showErrors ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </h3>
            </div>
            <div class="card-body p-0" x-show="showErrors" x-transition>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="table-custom">
                        <thead><tr><th>Linea</th><th>Info</th><th>Razon</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($errors, 0, 50) as $err)
                            <tr>
                                <td>{{ $err['line'] }}</td>
                                <td class="font-medium text-xs">{{ $err['info'] }}</td>
                                <td class="text-red-600 text-xs">{{ $err['reason'] }}</td>
                            </tr>
                            @endforeach
                            @if(count($errors) > 50)
                            <tr><td colspan="3" class="text-center text-slate_custom-400 text-xs">... y {{ count($errors) - 50 }} errores mas</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Preview table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-eye mr-2 text-burgundy-800"></i>Muestra de datos (primeras {{ count($previewRows) }} filas validas)
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr><th>Linea</th><th>COD_EDIF</th><th>NUM_APTO</th><th>Periodo</th><th>Monto</th><th>Serial</th></tr>
                        </thead>
                        <tbody>
                            @foreach($previewRows as $row)
                            <tr>
                                <td class="text-xs">{{ $row['line'] }}</td>
                                <td class="text-xs">{{ $row['display']['cod_edif'] }}</td>
                                <td class="font-medium text-sm">{{ $row['display']['num_apto'] }}</td>
                                <td class="text-xs">{{ $row['display']['periodo'] }}</td>
                                <td class="text-xs font-semibold">{{ $row['display']['monto'] }}</td>
                                <td class="text-xs">{{ $row['display']['serial'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Execute --}}
        @if($summary['validas'] > 0)
        <form action="{{ route('financiero.deudas.importar.execute') }}" method="POST"
              onsubmit="return confirm('ATENCION: Se eliminaran {{ number_format($summary['total_actual_bd']) }} deudas actuales y se insertaran {{ number_format($summary['validas']) }} nuevas. Esta seguro?')">
            @csrf
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('financiero.deudas.importar') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                <button type="submit" class="btn-primary"
                        onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Procesando...'; this.form.submit();">
                    <i class="fas fa-sync-alt mr-2"></i>Reemplazar Deudas ({{ number_format($summary['validas']) }} filas)
                </button>
            </div>
        </form>
        @endif
    </div>

    {{-- ==================== UPLOAD ==================== --}}
    @else
    <div class="space-y-6">
        {{-- Current status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Deudas Actuales en BD</div>
                    <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-database text-navy-800"></i></div>
                </div>
                <div class="stat-value">{{ number_format($totalActual ?? 0) }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Ultima Carga</div>
                    <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-burgundy-800"></i></div>
                </div>
                <div class="stat-value text-sm">
                    {{ $ultimaCarga ? \Carbon\Carbon::parse($ultimaCarga)->format('d/m/Y H:i') : 'Nunca' }}
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo de Deudas</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('financiero.deudas.importar.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-xl mx-auto text-center">
                        <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-invoice-dollar text-2xl text-burgundy-800"></i>
                        </div>
                        <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Carga Completa Diaria</h4>
                        <p class="text-sm text-slate_custom-400 mb-2">Este proceso <strong class="text-red-600">elimina todas las deudas actuales</strong> y las reemplaza con las del archivo.</p>
                        <p class="text-sm text-slate_custom-400 mb-6">Formato pipe-delimited (<code>|</code>). Max 50MB.</p>
                        <div class="mb-6">
                            <input type="file" name="archivo" accept=".csv,.txt,.dat" required
                                   class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                            @error('archivo') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-eye mr-2"></i>Vista Previa</button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-slate_custom-200">
                    <h5 class="text-sm font-heading font-semibold text-navy-800 mb-3"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Informacion</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-sync-alt text-amber-500 mt-0.5"></i>
                            <span><strong>Carga completa:</strong> Todas las deudas se reemplazan cada dia</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Edificios y apartamentos deben estar importados previamente</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Se validan COD_EDIF + NUM_APTO contra la base de datos</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate_custom-500">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Insercion en bloques de 500 para maximo rendimiento</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
