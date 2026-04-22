@extends('layouts.app')

@section('title', 'Afiliaciones PagoIntegral')

@section('content')

<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-heading font-bold text-navy-800">Afiliaciones PagoIntegral</h2>
        <p class="text-sm text-slate_custom-400 mt-1">Gestion de afiliaciones al sistema de debito automatico</p>
    </div>
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('financiero.pago-integral.afiliaciones.crear') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Nueva Afiliacion
        </a>
        <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
    </div>
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

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-value text-navy-800">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Registros</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-green-600">{{ $stats['activos'] }}</div>
        <div class="stat-label">Afiliados Activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-yellow-600">{{ $stats['pendientes'] }}</div>
        <div class="stat-label">Nuevas Afiliaciones</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-red-600">{{ $stats['desafiliados'] }}</div>
        <div class="stat-label">Desafiliaciones</div>
    </div>
</div>

{{-- Panel Proceso Mercantil --}}
<div class="card mb-6 border-l-4 border-l-amber-500">
    <div class="card-header flex items-center gap-2">
        <i class="fas fa-university text-amber-600"></i>
        <h3 class="text-sm font-heading font-semibold text-navy-800">Proceso Mercantil — Generacion de Archivos</h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Generar Mdomi --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex flex-col gap-3">
                <div>
                    <div class="font-semibold text-amber-800 text-sm flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Afiliaciones (Mdomi)
                    </div>
                    <div class="text-xs text-slate_custom-500 mt-1">Afiliaciones Mercantil pendientes sin archivo enviado</div>
                    <div class="mt-2">
                        @if($mercantilPendientesAfil > 0)
                            <span class="text-2xl font-bold text-amber-700">{{ $mercantilPendientesAfil }}</span>
                            <span class="text-xs text-amber-600 ml-1">listas para enviar</span>
                        @else
                            <span class="text-sm text-slate_custom-400 italic">Sin pendientes</span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('financiero.pago-integral.afiliaciones.mercantil.generar') }}">
                    @csrf
                    <input type="hidden" name="tipo_operacion" value="A">
                    <button type="submit"
                            @if($mercantilPendientesAfil === 0) disabled @endif
                            class="w-full inline-flex items-center justify-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg transition-colors
                                {{ $mercantilPendientesAfil > 0 ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-100 text-slate_custom-400 cursor-not-allowed' }}"
                            @if($mercantilPendientesAfil > 0)
                            onclick="return confirm('¿Generar archivo Mdomi con {{ $mercantilPendientesAfil }} afiliacion(es)?')"
                            @endif>
                        <i class="fas fa-file-download"></i> Generar Mdomi
                    </button>
                </form>
            </div>

            {{-- Generar Mdesdomi --}}
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 flex flex-col gap-3">
                <div>
                    <div class="font-semibold text-orange-800 text-sm flex items-center gap-2">
                        <i class="fas fa-user-minus"></i> Desafiliaciones (Mdesdomi)
                    </div>
                    <div class="text-xs text-slate_custom-500 mt-1">Desafiliaciones Mercantil sin archivo enviado</div>
                    <div class="mt-2">
                        @if($mercantilPendientesDesafil > 0)
                            <span class="text-2xl font-bold text-orange-700">{{ $mercantilPendientesDesafil }}</span>
                            <span class="text-xs text-orange-600 ml-1">listas para enviar</span>
                        @else
                            <span class="text-sm text-slate_custom-400 italic">Sin pendientes</span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('financiero.pago-integral.afiliaciones.mercantil.generar') }}">
                    @csrf
                    <input type="hidden" name="tipo_operacion" value="D">
                    <button type="submit"
                            @if($mercantilPendientesDesafil === 0) disabled @endif
                            class="w-full inline-flex items-center justify-center gap-2 text-sm font-semibold px-4 py-2 rounded-lg transition-colors
                                {{ $mercantilPendientesDesafil > 0 ? 'bg-orange-600 hover:bg-orange-700 text-white' : 'bg-slate-100 text-slate_custom-400 cursor-not-allowed' }}"
                            @if($mercantilPendientesDesafil > 0)
                            onclick="return confirm('¿Generar archivo Mdesdomi con {{ $mercantilPendientesDesafil }} desafiliacion(es)?')"
                            @endif>
                        <i class="fas fa-file-download"></i> Generar Mdesdomi
                    </button>
                </form>
            </div>

            {{-- Procesar Respuesta --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex flex-col gap-3">
                <div>
                    <div class="font-semibold text-blue-800 text-sm flex items-center gap-2">
                        <i class="fas fa-file-upload"></i> Respuesta del Banco
                    </div>
                    <div class="text-xs text-slate_custom-500 mt-1">Cargar respuesta de Mercantil para actualizar estatus</div>
                    <div class="mt-2">
                        @if($mercantilEsperandoRespuesta > 0)
                            <span class="text-2xl font-bold text-blue-700">{{ $mercantilEsperandoRespuesta }}</span>
                            <span class="text-xs text-blue-600 ml-1">esperando respuesta</span>
                        @else
                            <span class="text-sm text-slate_custom-400 italic">Sin archivos enviados</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('financiero.pago-integral.afiliaciones.mercantil.respuesta.form') }}"
                   class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-cogs"></i> Procesar Respuesta
                </a>
            </div>

        </div>
    </div>
</div>

{{-- Pestanas + Filtros + Tabla --}}
@php $tabActual = request('tab', 'afiliaciones'); @endphp

<div class="card">
    {{-- Pestanas --}}
    <div class="flex border-b border-slate-200 overflow-x-auto">
        {{-- Afiliaciones (activos) --}}
        <a href="{{ route('financiero.pago-integral.afiliaciones', ['tab' => 'afiliaciones']) }}"
           class="flex items-center gap-2 px-6 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
               {{ $tabActual === 'afiliaciones'
                   ? 'border-navy-800 text-navy-800 bg-blue-50'
                   : 'border-transparent text-slate_custom-500 hover:text-navy-800 hover:border-slate-300' }}">
            <i class="fas fa-users"></i>
            Listado de Afiliaciones
            <span class="ml-1 text-xs px-2 py-0.5 rounded-full font-bold
                {{ $tabActual === 'afiliaciones' ? 'bg-navy-800 text-white' : 'bg-slate-200 text-slate_custom-500' }}">
                {{ $stats['activos'] }}
            </span>
        </a>
        {{-- Nuevas afiliaciones (pendientes) --}}
        <a href="{{ route('financiero.pago-integral.afiliaciones', ['tab' => 'nuevas']) }}"
           class="flex items-center gap-2 px-6 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
               {{ $tabActual === 'nuevas'
                   ? 'border-amber-500 text-amber-700 bg-amber-50'
                   : 'border-transparent text-slate_custom-500 hover:text-navy-800 hover:border-slate-300' }}">
            <i class="fas fa-user-plus"></i>
            Nuevas Afiliaciones
            <span class="ml-1 text-xs px-2 py-0.5 rounded-full font-bold
                {{ $tabActual === 'nuevas' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate_custom-500' }}">
                {{ $stats['pendientes'] }}
            </span>
        </a>
        {{-- Desafiliaciones --}}
        <a href="{{ route('financiero.pago-integral.afiliaciones', ['tab' => 'desafiliaciones']) }}"
           class="flex items-center gap-2 px-6 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-colors
               {{ $tabActual === 'desafiliaciones'
                   ? 'border-red-600 text-red-700 bg-red-50'
                   : 'border-transparent text-slate_custom-500 hover:text-navy-800 hover:border-slate-300' }}">
            <i class="fas fa-user-minus"></i>
            Desafiliaciones
            <span class="ml-1 text-xs px-2 py-0.5 rounded-full font-bold
                {{ $tabActual === 'desafiliaciones' ? 'bg-red-600 text-white' : 'bg-slate-200 text-slate_custom-500' }}">
                {{ $stats['desafiliados'] }}
            </span>
        </a>
    </div>

    {{-- Filtros --}}
    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
        <form method="GET" action="{{ route('financiero.pago-integral.afiliaciones') }}"
              class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="{{ $tabActual }}">
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Cedula / RIF</label>
                <input type="text" name="cedula" value="{{ request('cedula') }}"
                       placeholder="Buscar cedula..."
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-32">
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ request('nombre') }}"
                       placeholder="Buscar nombre..."
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-36">
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Banco</label>
                <select name="banco_id"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-32">
                    <option value="">Todos</option>
                    @foreach($bancos as $banco)
                        <option value="{{ $banco->id }}" {{ request('banco_id') == $banco->id ? 'selected' : '' }}>
                            {{ $banco->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Estatus</label>
                <select name="estatus"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-28">
                    <option value="">Todos</option>
                    <option value="A" {{ request('estatus') === 'A' ? 'selected' : '' }}>Activo</option>
                    <option value="I" {{ request('estatus') === 'I' ? 'selected' : '' }}>Inactivo</option>
                    <option value="P" {{ request('estatus') === 'P' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            @if($tabActual === 'afiliaciones')
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Est. Mercantil</label>
                <select name="mercantil_estatus"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-36">
                    <option value="">Todos</option>
                    <option value="P" {{ request('mercantil_estatus') === 'P' ? 'selected' : '' }}>Pend. respuesta</option>
                    <option value="A" {{ request('mercantil_estatus') === 'A' ? 'selected' : '' }}>Aprobado</option>
                    <option value="R" {{ request('mercantil_estatus') === 'R' ? 'selected' : '' }}>Rechazado</option>
                </select>
            </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-32">
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-32">
            </div>
            <button type="submit" class="btn-primary py-1.5 px-4 text-sm">
                <i class="fas fa-search mr-1"></i>Buscar
            </button>
            <a href="{{ route('financiero.pago-integral.afiliaciones', ['tab' => $tabActual]) }}" class="btn-secondary py-1.5 px-4 text-sm">
                <i class="fas fa-times mr-1"></i>Limpiar
            </a>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="card-header flex items-center justify-between border-t-0">
        <span class="text-sm font-semibold text-slate_custom-500">
            {{ $afiliaciones->total() }} registro(s) encontrado(s)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CEDULA</th>
                        <th>NOMBRE COMPLETO</th>
                        <th>BANCO</th>
                        <th>CUENTA</th>
                        <th>EDIFICIO / APTO</th>
                        <th>ESTATUS</th>
                        <th>FECHA</th>
                        @if($tabActual !== 'desafiliaciones')
                        <th>MERCANTIL</th>
                        @endif
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($afiliaciones as $afil)
                    <tr class="{{ $afil->estatus === 'I' ? 'opacity-60' : '' }}">
                        <td class="text-xs text-slate_custom-400">{{ $afiliaciones->firstItem() + $loop->index }}</td>
                        <td class="font-mono text-xs font-semibold">{{ ($afil->letra ?? '') . $afil->cedula_rif }}</td>
                        <td>
                            <div class="font-semibold text-navy-800 text-sm">{{ $afil->nombres }} {{ $afil->apellidos }}</div>
                            @if($afil->email)
                                <div class="text-xs text-slate_custom-400">{{ $afil->email }}</div>
                            @endif
                        </td>
                        <td class="text-xs whitespace-nowrap">
                            {{ $afil->banco->nombre ?? '—' }}
                            @if($afil->esMercantil())
                                <span class="ml-1 text-amber-600" title="Banco Mercantil - proceso dos pasos">
                                    <i class="fas fa-university"></i>
                                </span>
                            @endif
                        </td>
                        <td class="font-mono text-xs">{{ $afil->cta_bancaria ?? '—' }}</td>
                        <td class="text-xs">
                            <div class="font-semibold">{{ $afil->afilapto->edificio->nombre ?? '—' }}</div>
                            <div class="text-slate_custom-400">Apto: {{ $afil->afilapto->apartamento->num_apto ?? '—' }}</div>
                        </td>
                        <td>
                            @if($afil->estatus === 'A')
                                <span class="badge-success">Activo</span>
                            @elseif($afil->estatus === 'I')
                                <span class="badge-danger">Inactivo</span>
                            @elseif($afil->estatus === 'P')
                                <span class="badge-warning">Pendiente</span>
                            @else
                                <span class="badge-info">{{ $afil->estatus }}</span>
                            @endif
                        </td>
                        <td class="text-xs whitespace-nowrap">
                            {{ $afil->fecha ? \Carbon\Carbon::parse($afil->fecha)->format('d/m/Y') : '—' }}
                        </td>
                        @if($tabActual !== 'desafiliaciones')
                        <td class="text-xs">
                            @if($afil->esMercantil())
                                @if($afil->mercantil_estatus_proceso === 'P')
                                    <div class="flex flex-col gap-0.5">
                                        <span class="badge-warning">Pend. respuesta</span>
                                        @if($afil->mercantil_archivo_enviado)
                                            <span class="text-slate_custom-400 font-mono text-xs">{{ $afil->mercantil_archivo_enviado }}</span>
                                        @else
                                            <span class="text-slate_custom-400 italic">Sin archivo</span>
                                        @endif
                                    </div>
                                @elseif($afil->mercantil_estatus_proceso === 'A')
                                    <div class="flex flex-col gap-0.5">
                                        <span class="badge-success">Aprobado</span>
                                        @if($afil->mercantil_fecha_respuesta)
                                            <span class="text-slate_custom-400">{{ \Carbon\Carbon::parse($afil->mercantil_fecha_respuesta)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                @elseif($afil->mercantil_estatus_proceso === 'R')
                                    <div class="flex flex-col gap-0.5">
                                        <span class="badge-danger">Rechazado</span>
                                        @if($afil->mercantil_cod_respuesta)
                                            <span class="font-mono text-red-600">{{ $afil->mercantil_cod_respuesta }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate_custom-400 italic">Pend. envio</span>
                                @endif
                            @else
                                <span class="text-slate_custom-300">—</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('financiero.pago-integral.afiliaciones.edit', $afil) }}"
                                   class="btn-secondary text-xs py-1 px-3"
                                   title="Editar">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                @if($tabActual !== 'desafiliaciones' && $afil->estatus !== 'I')
                                <form method="POST"
                                      action="{{ route('financiero.pago-integral.afiliaciones.desafiliar', $afil) }}"
                                      onsubmit="return confirm('¿Confirma desafiliar a {{ addslashes($afil->nombres . ' ' . $afil->apellidos) }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-xs py-1 px-3 rounded-lg font-semibold border border-red-300 text-red-700 bg-red-50 hover:bg-red-700 hover:text-white hover:border-red-700 transition-colors">
                                        <i class="fas fa-user-times mr-1"></i>Desafiliar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $tabActual !== 'desafiliaciones' ? 10 : 9 }}" class="text-center text-slate_custom-400 py-10">
                            <i class="fas fa-users-slash text-4xl mb-3 block text-slate_custom-300"></i>
                            @if($tabActual === 'desafiliaciones')
                                No hay desafiliaciones que coincidan con los filtros
                            @elseif($tabActual === 'nuevas')
                                No hay nuevas afiliaciones pendientes
                            @else
                                No hay afiliaciones que coincidan con los filtros
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate_custom-200">
            {{ $afiliaciones->withQueryString()->links() }}
        </div>
    </div>
</div>

@endsection
