<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Importar Abonos</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Carga incremental — agrega nuevos abonos sin eliminar los existentes</p>
            </div>
            <a href="{{ route('financiero.cobranza.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver a Cobranza</a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>
    @endif

    {{-- RESULTS --}}
    @if(isset($results))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Anteriores en BD</div><div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-slate_custom-500"></i></div></div><div class="stat-value text-slate_custom-500">{{ number_format($results['previous_count']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Nuevos Importados</div><div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div></div><div class="stat-value text-green-600">{{ number_format($results['imported']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Duplicados Omitidos</div><div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-clone text-amber-600"></i></div></div><div class="stat-value text-amber-600">{{ number_format($results['skipped'] ?? 0) }}</div></div>
        </div>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2"><i class="fas fa-check-circle"></i>Carga incremental completada. {{ number_format($results['imported']) }} nuevos registros importados, {{ number_format($results['skipped'] ?? 0) }} duplicados omitidos.</div>
        @if(count($results['errors']) > 0)
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show"><h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full"><span><i class="fas fa-exclamation-triangle mr-2"></i>Errores ({{ count($results['errors']) }})</span><i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i></h3></div>
            <div class="card-body p-0" x-show="show" x-transition><div class="overflow-x-auto max-h-64 overflow-y-auto"><table class="table-custom"><thead><tr><th>Info</th><th>Razon</th></tr></thead><tbody>@foreach(array_slice($results['errors'], 0, 100) as $err)<tr><td class="text-xs font-medium">{{ $err['info'] }}</td><td class="text-red-600 text-xs">{{ \Illuminate\Support\Str::limit($err['reason'], 100) }}</td></tr>@endforeach</tbody></table></div></div>
        </div>
        @endif
        <div class="flex gap-3">
            <a href="{{ route('financiero.cobranza.index') }}" class="btn-primary"><i class="fas fa-hand-holding-usd mr-2"></i>Ir a Cobranza</a>
            <a href="{{ route('financiero.abonos.importar') }}" class="btn-secondary"><i class="fas fa-file-import mr-2"></i>Nueva Carga</a>
        </div>
    </div>

    {{-- PREVIEW --}}
    @elseif(isset($summary))
    <div class="space-y-6">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl">
            <div class="flex items-start gap-3"><i class="fas fa-plus-circle text-xl mt-0.5"></i><div><p class="font-heading font-bold">Carga Incremental — Se agregaran nuevos registros</p><p class="text-sm mt-1">Se incorporaran <strong>{{ number_format($summary['validas']) }}</strong> registros del archivo. Los duplicados se omitiran. Los <strong>{{ number_format($summary['total_actual_bd']) }}</strong> existentes se mantienen.</p></div></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Filas Archivo</div><div class="w-10 h-10 bg-slate_custom-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-alt text-slate_custom-500"></i></div></div><div class="stat-value">{{ number_format($summary['total_archivo']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Validas</div><div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div></div><div class="stat-value text-green-600">{{ number_format($summary['validas']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Con Errores</div><div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div></div><div class="stat-value text-red-600">{{ number_format($summary['errores']) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Actuales BD</div><div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-database text-blue-600"></i></div></div><div class="stat-value text-blue-600">{{ number_format($summary['total_actual_bd']) }}</div><p class="text-xs text-slate_custom-400 mt-1">Se mantienen</p></div>
        </div>
        @if(count($errors) > 0)
        <div class="card" x-data="{ show: false }">
            <div class="card-header cursor-pointer" @click="show = !show"><h3 class="text-sm font-heading font-semibold text-red-600 flex items-center justify-between w-full"><span><i class="fas fa-exclamation-triangle mr-2"></i>Filas con errores ({{ count($errors) }})</span><i class="fas" :class="show ? 'fa-chevron-up' : 'fa-chevron-down'"></i></h3></div>
            <div class="card-body p-0" x-show="show" x-transition><div class="overflow-x-auto max-h-64 overflow-y-auto"><table class="table-custom"><thead><tr><th>Linea</th><th>Info</th><th>Razon</th></tr></thead><tbody>@foreach(array_slice($errors, 0, 50) as $err)<tr><td>{{ $err['line'] }}</td><td class="text-xs font-medium">{{ $err['info'] }}</td><td class="text-red-600 text-xs">{{ $err['reason'] }}</td></tr>@endforeach</tbody></table></div></div>
        </div>
        @endif
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-eye mr-2 text-burgundy-800"></i>Muestra (primeras {{ count($previewRows) }})</h3></div>
            <div class="card-body p-0"><div class="overflow-x-auto"><table class="table-custom"><thead><tr><th>Linea</th><th>COD_EDIF</th><th>NUM_APTO</th><th>Periodo</th><th>Monto</th><th>Tipo</th><th>Serial</th></tr></thead><tbody>
                @foreach($previewRows as $row)
                <tr><td class="text-xs">{{ $row['line'] }}</td><td class="text-xs">{{ $row['display']['cod_edif'] }}</td><td class="font-medium text-sm">{{ $row['display']['num_apto'] }}</td><td class="text-xs">{{ $row['display']['periodo'] }}</td><td class="text-xs font-semibold">{{ $row['display']['monto'] }}</td><td class="text-xs">{{ $row['display']['tipo'] }}</td><td class="text-xs">{{ $row['display']['serial'] }}</td></tr>
                @endforeach
            </tbody></table></div></div>
        </div>
        @if($summary['validas'] > 0)
        <form action="{{ route('financiero.abonos.importar.execute') }}" method="POST" onsubmit="return confirm('Se importaran {{ number_format($summary['validas']) }} registros. Los duplicados se omitiran. Continuar?')">
            @csrf
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('financiero.abonos.importar') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                <button type="submit" class="btn-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Procesando...'; this.form.submit();"><i class="fas fa-file-import mr-2"></i>Importar Abonos ({{ number_format($summary['validas']) }})</button>
            </div>
        </form>
        @endif
    </div>

    {{-- UPLOAD --}}
    @else
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Abonos Actuales</div><div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-database text-navy-800"></i></div></div><div class="stat-value">{{ number_format($totalActual ?? 0) }}</div></div>
            <div class="stat-card"><div class="flex items-center justify-between"><div class="stat-label">Ultima Carga</div><div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-clock text-burgundy-800"></i></div></div><div class="stat-value text-sm">{{ $ultimaCarga ? \Carbon\Carbon::parse($ultimaCarga)->format('d/m/Y H:i') : 'Nunca' }}</div></div>
        </div>
        <div class="card">
            <div class="card-header"><h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-file-import mr-2 text-burgundy-800"></i>Subir Archivo de Abonos</h3></div>
            <div class="card-body">
                <form action="{{ route('financiero.abonos.importar.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-xl mx-auto text-center">
                        <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-money-bill-wave text-2xl text-burgundy-800"></i></div>
                        <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Carga Incremental de Abonos</h4>
                        <p class="text-sm text-slate_custom-400 mb-2">Agrega <strong class="text-blue-600">nuevos registros</strong> sin eliminar los existentes.</p>
                        <p class="text-sm text-slate_custom-400 mb-6">Formato pipe-delimited (<code>|</code>). Max 100MB.</p>
                        <div class="mb-6">
                            <input type="file" name="archivo" accept=".csv,.txt,.dat" required class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                            @error('archivo') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn-primary"><i class="fas fa-eye mr-2"></i>Vista Previa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
