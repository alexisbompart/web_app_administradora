<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pagos Pendientes de Aprobacion</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Pagos registrados por propietarios que requieren verificacion</p>
            </div>
            <a href="{{ route('financiero.cobranza.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Cobranza
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

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Pagos Pendientes</div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
            </div>
            <div class="stat-value text-amber-600">{{ $countPendiente }}</div>
            <p class="text-xs text-slate_custom-400 mt-1">Esperando verificacion</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div class="stat-label">Monto Total Pendiente</div>
                <div class="w-10 h-10 bg-burgundy-800/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-burgundy-800"></i>
                </div>
            </div>
            <div class="stat-value text-burgundy-800">{{ number_format($totalPendiente, 2, ',', '.') }} Bs</div>
            <p class="text-xs text-slate_custom-400 mt-1">Por confirmar</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list-check mr-2 text-burgundy-800"></i>Listado de Pagos
            </h3>
        </div>
        <div class="card-body p-0">
            @forelse($pendientes as $pago)
            <div class="border-b border-slate_custom-100 last:border-0 p-4" x-data="{ open: false, showRechazar: false }">
                <div class="flex items-start justify-between gap-4">
                    {{-- Info principal --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-heading font-bold text-navy-800 text-sm">#{{ $pago->id }}</span>
                            <span class="badge-warning text-xs">Pendiente</span>
                            <span class="text-xs text-slate_custom-400">
                                <i class="fas fa-calendar mr-1"></i>{{ $pago->fecha_pago?->format('d/m/Y') }}
                            </span>
                            <span class="text-xs text-slate_custom-400">
                                <i class="fas fa-user mr-1"></i>{{ $pago->registradoPor?->name ?? 'Propietario' }}
                            </span>
                        </div>
                        <div class="mt-2 flex items-center gap-4 flex-wrap text-sm">
                            <span class="text-slate_custom-500">
                                <i class="fas fa-university mr-1 text-slate_custom-400"></i>{{ $pago->banco?->nombre ?? '--' }}
                            </span>
                            <span class="text-slate_custom-500">
                                <i class="fas fa-hashtag mr-1 text-slate_custom-400"></i>Ref: <strong class="text-navy-800">{{ $pago->numero_referencia }}</strong>
                            </span>
                            <span class="text-slate_custom-500 capitalize">
                                <i class="fas fa-exchange-alt mr-1 text-slate_custom-400"></i>{{ $pago->forma_pago }}
                            </span>
                        </div>
                        {{-- Apartamentos involucrados --}}
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($pago->condPagoAptos->unique('apartamento_id') as $pa)
                            <span class="text-xs bg-slate_custom-100 text-navy-800 px-2 py-0.5 rounded-full">
                                {{ $pa->apartamento?->edificio?->nombre }} - {{ $pa->apartamento?->num_apto }}
                                @if($pa->periodo) ({{ $pa->periodo }})@endif
                            </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Monto y acciones --}}
                    <div class="flex-shrink-0 text-right">
                        <p class="text-xl font-heading font-bold text-burgundy-800">
                            {{ number_format($pago->monto_total, 2, ',', '.') }} Bs
                        </p>
                        <p class="text-xs text-slate_custom-400 mb-3">{{ $pago->condPagoAptos->count() }} recibo(s)</p>
                        <div class="flex items-center gap-2 justify-end">
                            <button @click="open = !open" class="btn-secondary text-xs py-1.5 px-3">
                                <i class="fas fa-eye mr-1"></i>Ver
                            </button>
                            @php $montoFmt = number_format($pago->monto_total, 2, ',', '.'); @endphp
                            <form action="{{ route('financiero.cobranza.aprobar-pago', $pago) }}" method="POST"
                                  onsubmit="return confirm('¿Confirmar aprobacion del pago #{{ $pago->id }} por {{ $montoFmt }} Bs?')">
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
                    <h5 class="text-xs font-semibold text-navy-800 mb-2">Recibos incluidos en este pago:</h5>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate_custom-50">
                                <th class="text-left px-2 py-1">Apartamento</th>
                                <th class="text-left px-2 py-1">Periodo</th>
                                <th class="text-right px-2 py-1">Monto Aplicado</th>
                                <th class="text-left px-2 py-1">Deuda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pago->condPagoAptos as $pa)
                            <tr class="border-b border-slate_custom-50">
                                <td class="px-2 py-1">{{ $pa->apartamento?->edificio?->nombre }} - {{ $pa->apartamento?->num_apto }}</td>
                                <td class="px-2 py-1">{{ $pa->periodo }}</td>
                                <td class="px-2 py-1 text-right font-semibold">{{ number_format($pa->monto_aplicado ?? 0, 2, ',', '.') }} Bs</td>
                                <td class="px-2 py-1">
                                    @if($pa->deuda)
                                        <span class="{{ $pa->deuda->estatus === 'C' ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $pa->deuda->estatus === 'C' ? 'Cancelada' : 'Pendiente' }}
                                        </span>
                                    @else
                                        <span class="text-slate_custom-400">--</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-navy-800 text-white">
                                <td colspan="2" class="px-2 py-1.5 text-right font-bold text-xs">TOTAL</td>
                                <td class="px-2 py-1.5 text-right font-bold text-xs">{{ number_format($pago->condPagoAptos->sum('monto_aplicado'), 2, ',', '.') }} Bs</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    @if($pago->observaciones)
                    <p class="text-xs text-slate_custom-400 mt-2"><i class="fas fa-comment mr-1"></i>{{ $pago->observaciones }}</p>
                    @endif
                </div>

                {{-- Form rechazo --}}
                <div x-show="showRechazar" x-transition class="mt-4 border-t border-red-100 pt-4">
                    <form action="{{ route('financiero.cobranza.rechazar-pago', $pago) }}" method="POST">
                        @csrf
                        <label class="block text-xs font-semibold text-red-700 mb-1">Motivo del rechazo <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" name="motivo" required placeholder="Ej: Referencia no encontrada, monto incorrecto..."
                                   class="flex-1 text-sm rounded-lg border-red-300 focus:border-red-500 focus:ring-red-500">
                            <button type="submit" class="text-sm py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition"
                                    onclick="return confirm('¿Rechazar este pago?')">
                                <i class="fas fa-times mr-1"></i>Confirmar Rechazo
                            </button>
                            <button type="button" @click="showRechazar = false" class="btn-secondary text-sm py-2">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-16 text-slate_custom-400">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-1">Sin pagos pendientes</h3>
                <p class="text-sm">No hay pagos de propietarios esperando aprobacion.</p>
            </div>
            @endforelse
        </div>
        @if($pendientes->hasPages())
        <div class="card-body border-t border-slate_custom-100">
            {{ $pendientes->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
