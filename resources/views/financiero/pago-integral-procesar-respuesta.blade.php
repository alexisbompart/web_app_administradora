<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Procesar Respuesta Bancaria</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    Archivo #{{ $archivo->id }} — {{ $archivo->banco->nombre ?? 'Sin banco' }}
                    &middot; {{ $archivo->nombre_archivo }}
                </p>
            </div>
            <a href="{{ route('financiero.pago-integral.archivos.detalle', $archivo) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver al Archivo
            </a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- RESULTADOS --}}
    @if(isset($results))
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Aprobados</div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
                </div>
                <div class="stat-value text-green-600">{{ $results['aprobados'] }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Deudas canceladas</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Rechazados</div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div>
                </div>
                <div class="stat-value text-red-600">{{ $results['rechazados'] }}</div>
                <p class="text-xs text-slate_custom-400 mt-1">Sin fondos / error</p>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">No Encontrados</div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center"><i class="fas fa-question-circle text-amber-600"></i></div>
                </div>
                <div class="stat-value text-amber-600">{{ $results['no_encontrados'] }}</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div class="stat-label">Total Procesados</div>
                    <div class="w-10 h-10 bg-navy-800/10 rounded-lg flex items-center justify-center"><i class="fas fa-list text-navy-800"></i></div>
                </div>
                <div class="stat-value text-navy-800">{{ count($results['detalles']) }}</div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            Respuesta bancaria procesada. {{ $results['aprobados'] }} pagos aprobados (deudas canceladas), {{ $results['rechazados'] }} rechazados.
        </div>

        {{-- Detalle de cada registro --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-list-alt mr-2 text-burgundy-800"></i>Detalle del Procesamiento
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cedula</th>
                                <th>Resultado</th>
                                <th>Mensaje del Banco</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results['detalles'] as $i => $det)
                            <tr>
                                <td class="text-xs text-slate_custom-400">{{ $i + 1 }}</td>
                                <td class="font-medium text-navy-800 text-sm">{{ $det['cedula'] }}</td>
                                <td>
                                    @if($det['estatus'] === 'Aprobado')
                                        <span class="badge-success text-xs"><i class="fas fa-check mr-1"></i>{{ $det['estatus'] }}</span>
                                    @elseif($det['estatus'] === 'Rechazado')
                                        <span class="badge-danger text-xs"><i class="fas fa-times mr-1"></i>{{ $det['estatus'] }}</span>
                                    @else
                                        <span class="badge-warning text-xs"><i class="fas fa-question mr-1"></i>{{ $det['estatus'] }}</span>
                                    @endif
                                </td>
                                <td class="text-xs text-slate_custom-500">{{ $det['mensaje'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('financiero.pago-integral.archivos.detalle', $archivo) }}" class="btn-primary">
                <i class="fas fa-file-alt mr-2"></i>Ver Archivo
            </a>
            <a href="{{ route('financiero.pago-integral.archivos') }}" class="btn-secondary">
                <i class="fas fa-list mr-2"></i>Todos los Archivos
            </a>
        </div>
    </div>

    {{-- FORMULARIO DE CARGA --}}
    @else
    <div class="space-y-6">
        {{-- Info del archivo --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="stat-label">Banco</div>
                <div class="stat-value text-sm">{{ $archivo->banco->nombre ?? '--' }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pagos en Archivo</div>
                <div class="stat-value">{{ $archivo->cantidad_pagos }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Monto Total</div>
                <div class="stat-value text-burgundy-800 text-sm">{{ number_format($archivo->monto_total, 2, ',', '.') }} Bs</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Estatus Actual</div>
                <div class="mt-1">
                    @if($archivo->estatus === 'GE')
                        <span class="badge-info">Generado</span>
                    @elseif($archivo->estatus === 'EN')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Enviado</span>
                    @elseif($archivo->estatus === 'EP')
                        <span class="badge-warning">En Proceso</span>
                    @elseif($archivo->estatus === 'PR')
                        <span class="badge-success">Procesado</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Explicacion --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm">
            <i class="fas fa-info-circle mr-2"></i>
            Suba el archivo de respuesta del banco <strong>{{ $archivo->banco->nombre ?? '' }}</strong>.
            El sistema leera cada registro y segun el resultado:
            <strong class="text-green-700">exitoso</strong> = aprueba pago y cancela deuda,
            <strong class="text-red-700">rechazado</strong> = marca el pago como rechazado.
        </div>

        {{-- Upload form --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-file-upload mr-2 text-burgundy-800"></i>Subir Archivo de Respuesta
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('financiero.pago-integral.archivos.procesar-respuesta.post', $archivo) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="max-w-xl mx-auto text-center">
                        <div class="w-16 h-16 bg-burgundy-800/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-university text-2xl text-burgundy-800"></i>
                        </div>
                        <h4 class="text-lg font-heading font-bold text-navy-800 mb-2">Respuesta de {{ $archivo->banco->nombre ?? 'Banco' }}</h4>
                        <p class="text-sm text-slate_custom-400 mb-1">
                            @if(strtoupper($archivo->banco->iniciales ?? '') === 'BC')
                                Formato Bancaribe: archivo <code>BCcobroXXXResp.txt</code>
                            @elseif(strtoupper($archivo->banco->iniciales ?? '') === 'BM')
                                Formato Mercantil: archivo <code>McobroXXXResp.txt</code>
                            @elseif(strtoupper($archivo->banco->iniciales ?? '') === 'BAN')
                                Formato Banesco: archivo <code>BcobroXXXResp.txt</code>
                            @else
                                Archivo de respuesta del banco
                            @endif
                        </p>
                        <p class="text-sm text-slate_custom-400 mb-6">Corresponde al archivo enviado: <strong>{{ $archivo->nombre_archivo }}</strong></p>
                        <div class="mb-6">
                            <input type="file" name="archivo_respuesta" accept=".txt,.csv,.dat" required
                                   class="w-full text-sm text-slate_custom-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-700 file:cursor-pointer">
                            @error('archivo_respuesta') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn-primary"
                                onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Procesando respuesta...'; this.form.submit();">
                            <i class="fas fa-cogs mr-2"></i>Procesar Respuesta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
