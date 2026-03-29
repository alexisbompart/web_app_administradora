<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    Archivo #{{ $archivo->id }} — {{ $archivo->banco->nombre ?? 'Sin banco' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-file-alt mr-1"></i>{{ $archivo->nombre_archivo }}
                    &middot; Generado {{ $archivo->fecha_generado?->format('d/m/Y H:i') }}
                    por {{ $archivo->generadoPor?->name ?? 'Sistema' }}
                </p>
            </div>
            <a href="{{ route('financiero.pago-integral.archivos') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Info del archivo y progreso --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Datos del archivo --}}
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-info-circle mr-2 text-burgundy-800"></i>Informacion del Archivo
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase font-semibold">Banco</p>
                        <p class="font-semibold text-navy-800">{{ $archivo->banco->nombre ?? '--' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase font-semibold">Tipo</p>
                        <p class="font-semibold text-navy-800">{{ str_replace('_', ' ', $archivo->tipo_archivo) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase font-semibold">Pagos</p>
                        <p class="font-semibold text-navy-800">{{ $archivo->cantidad_pagos }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate_custom-400 uppercase font-semibold">Monto Total</p>
                        <p class="font-bold text-burgundy-800">{{ number_format($archivo->monto_total, 2, ',', '.') }} Bs</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatus y acciones --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-tasks mr-2 text-burgundy-800"></i>Estatus
                </h3>
            </div>
            <div class="card-body text-center">
                @if($archivo->estatus === 'GE')
                    <span class="badge-info text-base px-4 py-1.5"><i class="fas fa-file-alt mr-2"></i>Generado</span>
                @elseif($archivo->estatus === 'EN')
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-base font-semibold bg-blue-100 text-blue-700"><i class="fas fa-paper-plane mr-2"></i>Enviado</span>
                @elseif($archivo->estatus === 'EP')
                    <span class="badge-warning text-base px-4 py-1.5"><i class="fas fa-spinner mr-2"></i>En Proceso</span>
                @elseif($archivo->estatus === 'PR')
                    <span class="badge-success text-base px-4 py-1.5"><i class="fas fa-check-double mr-2"></i>Procesado</span>
                @endif

                @if($archivo->estatus !== 'PR')
                <div class="mt-4 space-y-2">
                    @if($archivo->estatus === 'GE')
                    <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estatus" value="EN">
                        <button type="submit" class="w-full btn-primary text-sm">
                            <i class="fas fa-paper-plane mr-2"></i>Marcar como Enviado
                        </button>
                    </form>
                    @endif
                    @if(in_array($archivo->estatus, ['GE', 'EN']))
                    <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estatus" value="EP">
                        <button type="submit" class="w-full btn-secondary text-sm">
                            <i class="fas fa-spinner mr-2"></i>Marcar En Proceso
                        </button>
                    </form>
                    @endif
                    @if(in_array($archivo->estatus, ['EN', 'EP']))
                    <form action="{{ route('financiero.pago-integral.archivos.estatus', $archivo) }}" method="POST"
                          onsubmit="return confirm('Al marcar como Procesado se aprobaran automaticamente todos los pagos incluidos. Continuar?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="estatus" value="PR">
                        <button type="submit" class="w-full text-sm py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                            <i class="fas fa-check-double mr-2"></i>Procesado (Aprobar Pagos)
                        </button>
                    </form>
                    @endif
                </div>
                @endif

                {{-- Timeline --}}
                <div class="mt-5 text-left space-y-2">
                    <div class="flex items-center gap-2 text-xs">
                        <i class="fas fa-circle text-slate_custom-400"></i>
                        <span>Generado: {{ $archivo->fecha_generado?->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs {{ $archivo->fecha_enviado ? 'text-blue-600' : 'text-slate_custom-300' }}">
                        <i class="fas fa-circle"></i>
                        <span>Enviado: {{ $archivo->fecha_enviado?->format('d/m/Y H:i') ?? 'Pendiente' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs {{ $archivo->fecha_procesado ? 'text-green-600' : 'text-slate_custom-300' }}">
                        <i class="fas fa-circle"></i>
                        <span>Procesado: {{ $archivo->fecha_procesado?->format('d/m/Y H:i') ?? 'Pendiente' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagos incluidos en este archivo --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Pagos Incluidos ({{ $archivo->pagos->count() }})
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cedula</th>
                            <th>Afiliado</th>
                            <th>Edificio / Apto</th>
                            <th>Periodo(s)</th>
                            <th>Monto</th>
                            <th>Estatus Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivo->pagos as $pago)
                        <tr>
                            <td class="font-medium">#{{ $pago->id }}</td>
                            <td class="text-sm">{{ $pago->afilpagointegral?->letra }}{{ $pago->afilpagointegral?->cedula_rif }}</td>
                            <td>{{ $pago->afilpagointegral?->nombres }} {{ $pago->afilpagointegral?->apellidos }}</td>
                            <td>
                                @if($pago->afilpagointegral?->afilapto?->apartamento)
                                    {{ $pago->afilpagointegral->afilapto->apartamento->edificio?->nombre }} - {{ $pago->afilpagointegral->afilapto->apartamento->num_apto }}
                                @else
                                    --
                                @endif
                            </td>
                            <td class="text-xs">
                                @foreach($pago->pagoIntegralDetalles as $det)
                                    <span class="inline-block bg-slate_custom-100 text-navy-800 px-1.5 py-0.5 rounded mr-1 mb-1">{{ $det->periodo }}</span>
                                @endforeach
                            </td>
                            <td class="font-semibold">{{ number_format($pago->monto_total, 2, ',', '.') }} Bs</td>
                            <td>
                                @if($pago->estatus === 'A')
                                    <span class="badge-success">Aprobado</span>
                                @elseif($pago->estatus === 'R')
                                    <span class="badge-danger">Rechazado</span>
                                @else
                                    <span class="badge-warning">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-navy-800 text-white">
                            <td colspan="5" class="text-right font-bold text-sm py-2">TOTAL</td>
                            <td class="font-bold text-sm py-2">{{ number_format($archivo->pagos->sum('monto_total'), 2, ',', '.') }} Bs</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
