<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Aprobacion PagoIntegral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Listado de pagos integrales registrados</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.pago-integral.generar-archivo') }}" class="btn-primary">
                    <i class="fas fa-file-download mr-2"></i>Generar Archivo
                </a>
                <a href="{{ route('financiero.pago-integral.afiliaciones') }}" class="btn-secondary">
                    <i class="fas fa-users mr-2"></i>Afiliaciones
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Alerts --}}
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

    {{-- Search --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('financiero.pago-integral.aprobacion') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-navy-800 mb-1">Cedula</label>
                    <input type="text" name="cedula" value="{{ request('cedula') }}"
                           placeholder="Cedula"
                           class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-44">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-navy-800 mb-1">PINT</label>
                    <input type="text" name="pint" value="{{ request('pint') }}"
                           placeholder="PINT"
                           class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800 w-36">
                </div>
                <button type="submit" class="btn-primary py-2 px-4 text-sm">
                    <i class="fas fa-search mr-1"></i>Buscar
                </button>
                <a href="{{ route('financiero.pago-integral.aprobacion') }}" class="btn-secondary py-2 px-4 text-sm">
                    <i class="fas fa-times mr-1"></i>Limpiar
                </a>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalPendientes  = $pagos->getCollection()->where('estatus', 'P')->count();
        $totalAprobados   = $pagos->getCollection()->where('estatus', 'A')->count();
        $totalRechazados  = $pagos->getCollection()->where('estatus', 'R')->count();
    @endphp
    <div class="flex gap-4 mb-6 flex-wrap">
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm">
            <span class="text-xs font-semibold text-slate_custom-500">Pendientes en esta pagina:</span>
            <span class="badge-warning">{{ $totalPendientes }}</span>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm">
            <span class="text-xs font-semibold text-slate_custom-500">Aprobados en esta pagina:</span>
            <span class="badge-success">{{ $totalAprobados }}</span>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm">
            <span class="text-xs font-semibold text-slate_custom-500">Rechazados en esta pagina:</span>
            <span class="badge-danger">{{ $totalRechazados }}</span>
        </div>
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm">
            <span class="text-xs font-semibold text-slate_custom-500">Total registros:</span>
            <span class="font-bold text-navy-800">{{ $pagos->total() }}</span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Pagos Integrales
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>SUCURSAL</th>
                            <th>BANCO</th>
                            <th>CEDULA</th>
                            <th>NOMBRES</th>
                            <th>APELLIDOS</th>
                            <th>CTABANCARIA</th>
                            <th>INMUEBLE</th>
                            <th>NUM-APTO</th>
                            <th>PINT</th>
                            <th>FECHARECIBO</th>
                            <th>ESTATUS</th>
                            <th class="text-right">MONTO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        @php
                            $afil = $pago->afilpagointegral;
                        @endphp
                        <tr>
                            <td class="text-xs">
                                {{ $afil->afilapto->compania->nombre ?? ($afil->cod_sucursal ?? '—') }}
                            </td>
                            <td class="text-xs">{{ $afil->banco->nombre ?? '—' }}</td>
                            <td class="font-mono text-xs">{{ ($afil->letra ?? '') . $afil->cedula_rif }}</td>
                            <td>{{ $afil->nombres }}</td>
                            <td>{{ $afil->apellidos }}</td>
                            <td class="font-mono text-xs">{{ mb_substr($afil->cta_bancaria ?? '', 0, 20) }}</td>
                            <td class="text-xs">{{ $afil->afilapto->edificio->nombre ?? '—' }}</td>
                            <td class="text-xs">{{ $afil->afilapto->apartamento->num_apto ?? '—' }}</td>
                            <td class="font-mono text-xs font-bold">{{ str_pad($pago->id, 8, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-xs">{{ $pago->fecha->format('d/m/Y') }}</td>
                            <td>
                                @if($pago->estatus === 'P')
                                    <span class="badge-warning">PENDIENTE</span>
                                @elseif($pago->estatus === 'A')
                                    <span class="badge-success">APROBADO</span>
                                @elseif($pago->estatus === 'R')
                                    <span class="badge-danger">RECHAZADO</span>
                                @endif
                            </td>
                            <td class="text-right font-semibold text-navy-800 text-sm">
                                {{ number_format($pago->monto_total, 2, ',', '.') }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('financiero.pago-integral.comprobante', $pago) }}"
                                       class="text-navy-800 hover:text-burgundy-800 transition" title="Ver comprobante">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    @if($pago->estatus === 'P')
                                    <form method="POST" action="{{ route('financiero.pago-integral.aprobar', $pago) }}"
                                          onsubmit="return confirm('Aprobar este pago?')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Aprobar">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    <button type="button"
                                        onclick="document.getElementById('rechazar-modal-{{ $pago->id }}').classList.remove('hidden')"
                                        class="text-red-500 hover:text-red-700 transition" title="Rechazar">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Rechazar Modal --}}
                        @if($pago->estatus === 'P')
                        <div id="rechazar-modal-{{ $pago->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
                                <h3 class="font-heading font-bold text-navy-800 mb-4">Rechazar Pago #{{ str_pad($pago->id, 8, '0', STR_PAD_LEFT) }}</h3>
                                <form method="POST" action="{{ route('financiero.pago-integral.rechazar', $pago) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-semibold text-navy-800 mb-1">Motivo de rechazo</label>
                                        <textarea name="motivo" rows="3" required
                                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-burgundy-800"></textarea>
                                    </div>
                                    <div class="flex gap-3 justify-end">
                                        <button type="button"
                                            onclick="document.getElementById('rechazar-modal-{{ $pago->id }}').classList.add('hidden')"
                                            class="btn-secondary">Cancelar</button>
                                        <button type="submit" class="btn-primary">Rechazar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-slate_custom-400 py-8">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                No hay pagos integrales registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $pagos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
