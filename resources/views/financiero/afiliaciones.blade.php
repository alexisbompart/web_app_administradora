@extends('layouts.app')

@section('title', 'Afiliaciones PagoIntegral')

@section('content')

<div class="flex items-center justify-between flex-wrap gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-heading font-bold text-navy-800">Afiliaciones PagoIntegral</h2>
        <p class="text-sm text-slate_custom-400 mt-1">Gestion de afiliaciones al sistema de debito automatico</p>
    </div>
    <div class="flex gap-3">
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

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-value text-navy-800">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Afiliados</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-green-600">{{ $stats['activos'] }}</div>
        <div class="stat-label">Activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-yellow-600">{{ $stats['pendientes'] }}</div>
        <div class="stat-label">Pendientes</div>
    </div>
    <div class="stat-card">
        <div class="stat-value text-red-600">{{ $stats['inactivos'] }}</div>
        <div class="stat-label">Inactivos / Desafiliados</div>
    </div>
</div>

{{-- Filtros --}}
<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('financiero.pago-integral.afiliaciones') }}"
              class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Cedula / RIF</label>
                <input type="text" name="cedula" value="{{ request('cedula') }}"
                       placeholder="Buscar cedula..."
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-44">
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ request('nombre') }}"
                       placeholder="Buscar nombre..."
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-44">
            </div>
            <div>
                <label class="block text-xs font-semibold text-navy-800 mb-1">Estatus</label>
                <select name="estatus"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800">
                    <option value="">Todos</option>
                    <option value="A" {{ request('estatus') === 'A' ? 'selected' : '' }}>Activo</option>
                    <option value="I" {{ request('estatus') === 'I' ? 'selected' : '' }}>Inactivo</option>
                    <option value="P" {{ request('estatus') === 'P' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            <button type="submit" class="btn-primary py-2 px-4 text-sm">
                <i class="fas fa-search mr-1"></i>Buscar
            </button>
            <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary py-2 px-4 text-sm">
                <i class="fas fa-times mr-1"></i>Limpiar
            </a>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-header flex items-center justify-between">
        <h3 class="text-sm font-heading font-semibold text-navy-800">
            <i class="fas fa-users mr-2 text-burgundy-800"></i>
            Afiliados ({{ $afiliaciones->total() }})
        </h3>
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
                        <th>TIPO</th>
                        <th>EDIFICIO</th>
                        <th>APTO</th>
                        <th>ESTATUS</th>
                        <th>FECHA</th>
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
                        <td class="text-xs">{{ $afil->banco->nombre ?? '—' }}</td>
                        <td class="font-mono text-xs">{{ $afil->cta_bancaria ?? '—' }}</td>
                        <td class="text-xs">{{ $afil->tipo_cta ?? '—' }}</td>
                        <td class="text-xs">{{ $afil->afilapto->edificio->nombre ?? '—' }}</td>
                        <td class="text-xs font-semibold">{{ $afil->afilapto->apartamento->num_apto ?? '—' }}</td>
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
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('financiero.pago-integral.afiliaciones.edit', $afil) }}"
                                   class="btn-secondary text-xs py-1 px-3"
                                   title="Editar afiliacion">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>

                                @if($afil->estatus !== 'I')
                                <form method="POST"
                                      action="{{ route('financiero.pago-integral.afiliaciones.desafiliar', $afil) }}"
                                      onsubmit="return confirm('¿Confirma que desea desafiliar a {{ addslashes($afil->nombres . ' ' . $afil->apellidos) }}? Esta accion cambiara su estatus a Inactivo.')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-xs py-1 px-3 rounded-lg font-semibold border border-red-300 text-red-700 bg-red-50 hover:bg-red-700 hover:text-white hover:border-red-700 transition-colors"
                                            title="Desafiliar">
                                        <i class="fas fa-user-times mr-1"></i>Desafiliar
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-slate_custom-400 italic">Desafiliado</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-slate_custom-400 py-10">
                            <i class="fas fa-users-slash text-4xl mb-3 block text-slate_custom-300"></i>
                            No hay afiliaciones que coincidan con los filtros
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
