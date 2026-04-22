@extends('layouts.app')

@section('title', 'Procesar Respuesta Mercantil - Afiliaciones')

@section('content')

<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-heading font-bold text-navy-800">Respuesta Mercantil — Afiliaciones</h2>
        <p class="text-sm text-slate_custom-400 mt-1">Procesamiento del archivo de respuesta del Banco Mercantil</p>
    </div>
    <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Afiliaciones
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
    <i class="fas fa-times-circle"></i> {{ session('error') }}
</div>
@endif

{{-- Resultados del procesamiento --}}
@isset($resultados)
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="stat-card border-l-4 border-l-green-500">
        <div class="stat-value text-green-600">{{ $aprobados }}</div>
        <div class="stat-label">Aprobados</div>
    </div>
    <div class="stat-card border-l-4 border-l-red-500">
        <div class="stat-value text-red-600">{{ $rechazados }}</div>
        <div class="stat-label">Rechazados</div>
    </div>
    <div class="stat-card border-l-4 border-l-slate-400">
        <div class="stat-value text-slate_custom-500">{{ $noEncontrados }}</div>
        <div class="stat-label">No encontrados</div>
    </div>
</div>

<div class="card mb-6">
    <div class="card-header">
        <h3 class="text-sm font-heading font-semibold text-navy-800">
            <i class="fas fa-list-check mr-2 text-burgundy-800"></i>
            Detalle del Procesamiento ({{ count($resultados) }} registros)
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CEDULA</th>
                        <th>COD. RESPUESTA</th>
                        <th>MENSAJE</th>
                        <th>RESULTADO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resultados as $i => $res)
                    <tr>
                        <td class="text-xs text-slate_custom-400">{{ $i + 1 }}</td>
                        <td class="font-mono text-sm font-semibold">{{ $res['cedula'] }}</td>
                        <td class="font-mono text-sm">{{ $res['cod'] ?: '—' }}</td>
                        <td class="text-sm">{{ $res['mensaje'] }}</td>
                        <td>
                            @if($res['estado'] === 'aprobado')
                                <span class="badge-success"><i class="fas fa-check mr-1"></i>Aprobado</span>
                            @elseif($res['estado'] === 'rechazado')
                                <span class="badge-danger"><i class="fas fa-times mr-1"></i>Rechazado</span>
                            @else
                                <span class="badge-info"><i class="fas fa-question mr-1"></i>No encontrado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate_custom-400 py-8">
                            No se procesaron registros del archivo
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="flex gap-3 justify-end">
    <a href="{{ route('financiero.pago-integral.afiliaciones.mercantil.respuesta.form') }}" class="btn-primary">
        <i class="fas fa-upload mr-2"></i>Procesar Otro Archivo
    </a>
    <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
        <i class="fas fa-list mr-2"></i>Ver Afiliaciones
    </a>
</div>
@endisset

{{-- Formulario inicial --}}
@isset($pendientes)

@php
    // Agrupar por archivo para mostrar el botón anular por archivo
    $archivosAfil  = isset($pendientes['A']) ? $pendientes['A']->groupBy('mercantil_archivo_enviado') : collect();
    $archivosDesaf = isset($pendientes['D']) ? $pendientes['D']->groupBy('mercantil_archivo_enviado') : collect();
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Afiliaciones pendientes --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-plus mr-2 text-green-600"></i>
                Afiliaciones Pendientes de Respuesta
            </h3>
        </div>
        <div class="card-body space-y-4">
            @forelse($archivosAfil as $nombreArchivo => $registros)
                <div class="border border-amber-200 rounded-xl overflow-hidden">
                    <div class="bg-amber-50 px-3 py-2 flex items-center justify-between gap-3">
                        <div>
                            <span class="font-mono font-semibold text-amber-800 text-sm">{{ $nombreArchivo }}</span>
                            <span class="text-xs text-slate_custom-400 ml-2">
                                {{ $registros->count() }} registro(s) —
                                {{ $registros->first()->mercantil_fecha_envio ? \Carbon\Carbon::parse($registros->first()->mercantil_fecha_envio)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('financiero.pago-integral.afiliaciones.mercantil.anular') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="archivo" value="{{ $nombreArchivo }}">
                            <input type="hidden" name="tipo_operacion" value="A">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-300 text-red-700 bg-white hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors"
                                    onclick="return confirm('¿Anular el archivo {{ $nombreArchivo }}? Los {{ $registros->count() }} registros volvera a estar disponibles para generar un nuevo archivo.')">
                                <i class="fas fa-trash-alt"></i> Anular
                            </button>
                        </form>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-36 overflow-y-auto">
                        @foreach($registros as $afil)
                        <div class="flex items-center justify-between text-xs px-3 py-1.5">
                            <span class="font-mono font-semibold">{{ ($afil->letra ?? '') . $afil->cedula_rif }}</span>
                            <span class="text-slate_custom-400">{{ $afil->nombres }} {{ $afil->apellidos }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center text-slate_custom-400 py-6 text-sm">
                    <i class="fas fa-check-circle text-2xl text-green-400 block mb-2"></i>
                    No hay afiliaciones esperando respuesta
                </div>
            @endforelse
        </div>
    </div>

    {{-- Desafiliaciones pendientes --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-minus mr-2 text-red-600"></i>
                Desafiliaciones Pendientes de Respuesta
            </h3>
        </div>
        <div class="card-body space-y-4">
            @forelse($archivosDesaf as $nombreArchivo => $registros)
                <div class="border border-orange-200 rounded-xl overflow-hidden">
                    <div class="bg-orange-50 px-3 py-2 flex items-center justify-between gap-3">
                        <div>
                            <span class="font-mono font-semibold text-orange-800 text-sm">{{ $nombreArchivo }}</span>
                            <span class="text-xs text-slate_custom-400 ml-2">
                                {{ $registros->count() }} registro(s) —
                                {{ $registros->first()->mercantil_fecha_envio ? \Carbon\Carbon::parse($registros->first()->mercantil_fecha_envio)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('financiero.pago-integral.afiliaciones.mercantil.anular') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="archivo" value="{{ $nombreArchivo }}">
                            <input type="hidden" name="tipo_operacion" value="D">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg border border-red-300 text-red-700 bg-white hover:bg-red-600 hover:text-white hover:border-red-600 transition-colors"
                                    onclick="return confirm('¿Anular el archivo {{ $nombreArchivo }}? Los {{ $registros->count() }} registros volveara a estar disponibles para generar un nuevo archivo.')">
                                <i class="fas fa-trash-alt"></i> Anular
                            </button>
                        </form>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-36 overflow-y-auto">
                        @foreach($registros as $afil)
                        <div class="flex items-center justify-between text-xs px-3 py-1.5">
                            <span class="font-mono font-semibold">{{ ($afil->letra ?? '') . $afil->cedula_rif }}</span>
                            <span class="text-slate_custom-400">{{ $afil->nombres }} {{ $afil->apellidos }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center text-slate_custom-400 py-6 text-sm">
                    <i class="fas fa-check-circle text-2xl text-green-400 block mb-2"></i>
                    No hay desafiliaciones esperando respuesta
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Formulario para cargar archivo de respuesta --}}
<div class="card">
    <div class="card-header">
        <h3 class="text-sm font-heading font-semibold text-navy-800">
            <i class="fas fa-file-upload mr-2 text-burgundy-800"></i>
            Cargar Archivo de Respuesta del Banco
        </h3>
    </div>
    <div class="card-body">
        <form method="POST"
              action="{{ route('financiero.pago-integral.afiliaciones.mercantil.respuesta') }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                El archivo de respuesta del Banco Mercantil contiene los resultados de las afiliaciones o desafiliaciones enviadas.
                Seleccione el tipo de operacion que corresponde al archivo.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-2">
                        Tipo de Operacion <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo_operacion" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                        <option value="">-- Seleccione --</option>
                        <option value="A">Afiliaciones (Mdomi)</option>
                        <option value="D">Desafiliaciones (Mdesdomi)</option>
                    </select>
                    @error('tipo_operacion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-800 mb-2">
                        Archivo de Respuesta (.txt / .dat) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="archivo_respuesta" required
                           accept=".txt,.dat"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-burgundy-800 file:text-white hover:file:bg-burgundy-900">
                    @error('archivo_respuesta')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-800 space-y-1">
                <div class="font-semibold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i>Formato esperado del archivo:</div>
                <div>• Linea 1: Cabecera (comienza con "1BAMRVECA...")</div>
                <div>• Lineas siguientes: Detalle (comienzan con "2A" para afiliaciones o "2D" para desafiliaciones)</div>
                <div>• Cedula en posicion 2-10 (9 caracteres con ceros a la izquierda)</div>
                <div>• Codigo de respuesta en posicion 100-104 (4 caracteres) — "0074" o "0000" = Aprobado</div>
                <div>• Mensaje de respuesta en posicion 104-144 (40 caracteres)</div>
            </div>

            <div class="flex gap-3 justify-end pt-2">
                <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-cogs mr-2"></i>Procesar Archivo
                </button>
            </div>
        </form>
    </div>
</div>
@endisset

@endsection
