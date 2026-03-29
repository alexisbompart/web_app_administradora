<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Aprobacion de pagos integrales del condominio</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('financiero.pago-integral.archivos') }}" class="btn-primary">
                    <i class="fas fa-folder-open mr-2"></i>Archivos Bancarios
                </a>
                <a href="{{ route('financiero.pago-integral.consultar-saldo') }}" class="btn-secondary">
                    <i class="fas fa-search-dollar mr-2"></i>Consultar Saldo
                </a>
            </div>
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

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pendientes</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ $countPendiente }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Esperando aprobacion</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Monto Pendiente</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-burgundy-800">{{ number_format($totalPendiente, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Por confirmar</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Aprobados</div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="stat-value text-green-600">{{ $countAprobados }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Pagos confirmados</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Rechazados</div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
            <div class="stat-value text-red-600">{{ $countRechazados }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">No aprobados</p>
        </div>
    </div>

    {{-- Pagos Pendientes de Aprobacion - Separados por Banco --}}
    @if($pendientesPorBanco->isEmpty())
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list-check mr-2 text-burgundy-800"></i>Pagos Pendientes de Aprobacion
            </h3>
        </div>
        <div class="card-body">
            <div class="text-center py-16 text-slate_custom-400">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-1">Sin pagos pendientes</h3>
                <p class="text-sm">No hay pagos integrales esperando aprobacion.</p>
            </div>
        </div>
    </div>
    @else
    <div x-data="{ bancoTab: '{{ $pendientesPorBanco->keys()->first() }}' }" class="mb-6">
        {{-- Tabs de bancos --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($pendientesPorBanco as $bancoId => $pagos)
                @php
                    $banco = $bancosPendientes[$bancoId] ?? null;
                    $nombreBanco = $banco->nombre ?? 'Sin banco';
                    $iniciales = $banco->iniciales ?? '';
                    $totalBanco = $pagos->sum('monto_total');
                @endphp
                <button @click="bancoTab = '{{ $bancoId }}'"
                        :class="bancoTab === '{{ $bancoId }}' ? 'bg-navy-800 text-white shadow-lg' : 'bg-white text-navy-800 hover:bg-slate_custom-50 border border-slate_custom-200'"
                        class="flex items-center gap-3 px-5 py-3 rounded-xl font-medium transition-all duration-200">
                    <i class="fas fa-university"></i>
                    <div class="text-left">
                        <div class="text-sm font-semibold">{{ $nombreBanco }}</div>
                        <div class="text-xs opacity-75">{{ $pagos->count() }} pago(s) &middot; {{ number_format($totalBanco, 2, ',', '.') }} Bs</div>
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Contenido por banco --}}
        @foreach($pendientesPorBanco as $bancoId => $pagos)
        @php
            $banco = $bancosPendientes[$bancoId] ?? null;
            $nombreBanco = $banco->nombre ?? 'Sin banco';
        @endphp
        <div x-show="bancoTab === '{{ $bancoId }}'" x-transition class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-university mr-2 text-burgundy-800"></i>{{ $nombreBanco }} — {{ $pagos->count() }} pago(s) pendiente(s)
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-burgundy-800">
                        Total: {{ number_format($pagos->sum('monto_total'), 2, ',', '.') }} Bs
                    </span>
                    @if($banco)
                    <form method="POST" action="{{ route('financiero.pago-integral.generar-archivo.post') }}">
                        @csrf
                        <input type="hidden" name="banco_id" value="{{ $bancoId }}">
                        <input type="hidden" name="tipo_archivo" value="PAGOS_ENVIOS">
                        <button type="submit" class="btn-primary text-xs py-1.5 px-3">
                            <i class="fas fa-file-export mr-1"></i>Generar Archivo {{ $banco->iniciales ?? '' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($pagos as $pago)
                <div class="border-b border-slate_custom-100 last:border-0 p-4" x-data="{ open: false, showRechazar: false }">
                    <div class="flex items-start justify-between gap-4">
                        {{-- Info principal --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-heading font-bold text-navy-800 text-sm">#{{ $pago->id }}</span>
                                <span class="badge-warning text-xs">Pendiente</span>
                                <span class="text-xs text-slate_custom-400">
                                    <i class="fas fa-calendar mr-1"></i>{{ $pago->fecha?->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center gap-4 flex-wrap text-sm">
                                <span class="text-slate_custom-500">
                                    <i class="fas fa-user mr-1 text-slate_custom-400"></i>{{ $pago->afilpagointegral?->nombres }} {{ $pago->afilpagointegral?->apellidos }}
                                </span>
                                <span class="text-slate_custom-500">
                                    <i class="fas fa-id-card mr-1 text-slate_custom-400"></i>{{ $pago->afilpagointegral?->cedula_rif }}
                                </span>
                                <span class="text-slate_custom-500">
                                    <i class="fas fa-credit-card mr-1 text-slate_custom-400"></i>{{ $pago->afilpagointegral?->cta_bancaria }}
                                </span>
                                @if($pago->forma_pago)
                                <span class="text-slate_custom-500 capitalize">
                                    <i class="fas fa-exchange-alt mr-1 text-slate_custom-400"></i>{{ $pago->forma_pago }}
                                </span>
                                @endif
                                @if($pago->referencia)
                                <span class="text-slate_custom-500">
                                    <i class="fas fa-hashtag mr-1 text-slate_custom-400"></i>Ref: <strong class="text-navy-800">{{ $pago->referencia }}</strong>
                                </span>
                                @endif
                            </div>
                            {{-- Edificio / Apartamento --}}
                            <div class="mt-2 flex flex-wrap gap-1">
                                @if($pago->afilpagointegral?->afilapto?->apartamento)
                                <span class="text-xs bg-slate_custom-100 text-navy-800 px-2 py-0.5 rounded-full">
                                    {{ $pago->afilpagointegral->afilapto->apartamento->edificio?->nombre }} - Apto {{ $pago->afilpagointegral->afilapto->apartamento->num_apto }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Monto y acciones --}}
                        <div class="flex-shrink-0 text-right">
                            <p class="text-xl font-heading font-bold text-burgundy-800">
                                {{ number_format($pago->monto_total, 2, ',', '.') }} Bs
                            </p>
                            <p class="text-xs text-slate_custom-400 mb-3">{{ $pago->pagoIntegralDetalles->count() }} periodo(s)</p>
                            <div class="flex items-center gap-2 justify-end">
                                <button @click="open = !open" class="btn-secondary text-xs py-1.5 px-3">
                                    <i class="fas fa-eye mr-1"></i>Ver
                                </button>
                                @php $montoFmt = number_format($pago->monto_total, 2, ',', '.'); @endphp
                                <form action="{{ route('financiero.pago-integral.aprobar', $pago) }}" method="POST"
                                      onsubmit="return confirm('Confirmar aprobacion del pago #{{ $pago->id }} por {{ $montoFmt }} Bs?')">
                                    @csrf
                                    <button type="submit" class="btn-primary text-xs py-1.5 px-3">
                                        <i class="fas fa-check mr-1"></i>Aprobar
                                    </button>
                                </form>
                                <button @click="showRechazar = !showRechazar" class="text-xs py-1.5 px-3 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg font-medium transition">
                                    <i class="fas fa-times mr-1"></i>Rechazar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Detalle expandible --}}
                    <div x-show="open" x-transition class="mt-4 border-t border-slate_custom-100 pt-4">
                        <h5 class="text-xs font-semibold text-navy-800 mb-2">Periodos incluidos en este pago:</h5>
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate_custom-50">
                                    <th class="text-left px-2 py-1">Periodo</th>
                                    <th class="text-left px-2 py-1">Concepto</th>
                                    <th class="text-right px-2 py-1">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pago->pagoIntegralDetalles as $detalle)
                                <tr class="border-b border-slate_custom-50">
                                    <td class="px-2 py-1 font-medium">{{ $detalle->periodo }}</td>
                                    <td class="px-2 py-1">{{ $detalle->concepto }}</td>
                                    <td class="px-2 py-1 text-right font-semibold">{{ number_format($detalle->monto, 2, ',', '.') }} Bs</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-navy-800 text-white">
                                    <td colspan="2" class="px-2 py-1.5 text-right font-bold text-xs">TOTAL</td>
                                    <td class="px-2 py-1.5 text-right font-bold text-xs">{{ number_format($pago->pagoIntegralDetalles->sum('monto'), 2, ',', '.') }} Bs</td>
                                </tr>
                            </tfoot>
                        </table>
                        @if($pago->observaciones)
                        <p class="text-xs text-slate_custom-400 mt-2"><i class="fas fa-comment mr-1"></i>{{ $pago->observaciones }}</p>
                        @endif
                    </div>

                    {{-- Form rechazo --}}
                    <div x-show="showRechazar" x-transition class="mt-4 border-t border-red-100 pt-4">
                        <form action="{{ route('financiero.pago-integral.rechazar', $pago) }}" method="POST">
                            @csrf
                            <label class="block text-xs font-semibold text-red-700 mb-1">Motivo del rechazo <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" name="motivo" required placeholder="Ej: Referencia no encontrada, monto incorrecto..."
                                       class="flex-1 text-sm rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500">
                                <button type="submit" class="text-sm py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition"
                                        onclick="return confirm('Rechazar este pago?')">
                                    <i class="fas fa-times mr-1"></i>Confirmar Rechazo
                                </button>
                                <button type="button" @click="showRechazar = false" class="btn-secondary text-sm py-2">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Historial de pagos procesados --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-history mr-2 text-burgundy-800"></i>Historial de Pagos Procesados
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Afiliado</th>
                            <th>Banco</th>
                            <th>Edificio / Apto</th>
                            <th>Monto Total</th>
                            <th>Forma Pago</th>
                            <th>Referencia</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesados as $pago)
                        <tr>
                            <td class="font-medium">#{{ $pago->id }}</td>
                            <td>{{ $pago->fecha?->format('d/m/Y') }}</td>
                            <td>{{ $pago->afilpagointegral?->nombres }} {{ $pago->afilpagointegral?->apellidos }}</td>
                            <td>
                                <span class="text-xs font-medium">{{ $pago->afilpagointegral?->banco?->nombre ?? '--' }}</span>
                            </td>
                            <td>
                                @if($pago->afilpagointegral?->afilapto?->apartamento)
                                    {{ $pago->afilpagointegral->afilapto->apartamento->edificio?->nombre }} - {{ $pago->afilpagointegral->afilapto->apartamento->num_apto }}
                                @else
                                    --
                                @endif
                            </td>
                            <td class="font-semibold">{{ number_format($pago->monto_total, 2, ',', '.') }} Bs</td>
                            <td>{{ ucfirst($pago->forma_pago ?? '--') }}</td>
                            <td>{{ $pago->referencia ?? '--' }}</td>
                            <td>
                                @if($pago->estatus === 'A')
                                    <span class="badge-success">Aprobado</span>
                                @elseif($pago->estatus === 'R')
                                    <span class="badge-danger">Rechazado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('financiero.pago-integral.comprobante', $pago) }}" class="text-navy-800 hover:text-burgundy-800 transition" title="Ver comprobante">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay pagos procesados aun
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($procesados->hasPages())
            <div class="mt-4">
                {{ $procesados->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
