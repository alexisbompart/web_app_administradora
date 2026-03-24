<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pago Integral - Consultar Saldo</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Seleccione el afiliado y las deudas a pagar</p>
            </div>
            <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
    @endif

    <!-- Selector de Afiliado -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user-check mr-2 text-burgundy-800"></i>Seleccionar Afiliado
            </h3>
        </div>
        <div class="card-body">
            @if($afiliados->isEmpty())
            <div class="text-center py-8">
                <i class="fas fa-user-slash text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No hay afiliados activos disponibles.</p>
            </div>
            @else
            <form method="GET" action="{{ route('financiero.pago-integral.consultar-saldo') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-navy-800 mb-1">Afiliado</label>
                        <select name="afiliado_id" class="w-full rounded-lg border-slate_custom-300 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccione un afiliado --</option>
                            @foreach($afiliados as $af)
                            <option value="{{ $af->id }}" {{ request('afiliado_id') == $af->id ? 'selected' : '' }}>
                                {{ $af->cedula_rif }} - {{ $af->nombres }} {{ $af->apellidos }}
                                @if($af->afilapto?->apartamento)
                                    | {{ $af->afilapto->apartamento->edificio?->nombre }} Apto {{ $af->afilapto->apartamento->num_apto }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Consultar
                        </button>
                    </div>
                </div>
            </form>
            @endif
        </div>
    </div>

    @if($afiliado)
    <!-- Info del Afiliado -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-id-card mr-2 text-burgundy-800"></i>Datos del Afiliado
            </h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs text-slate_custom-400 block">Nombre</span>
                    <span class="text-sm font-semibold text-navy-800">{{ $afiliado->nombres }} {{ $afiliado->apellidos }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate_custom-400 block">Cedula/RIF</span>
                    <span class="text-sm font-semibold text-navy-800">{{ $afiliado->cedula_rif }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate_custom-400 block">Edificio</span>
                    <span class="text-sm font-semibold text-navy-800">{{ $afiliado->afilapto?->apartamento?->edificio?->nombre ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate_custom-400 block">Apartamento</span>
                    <span class="text-sm font-semibold text-navy-800">{{ $afiliado->afilapto?->apartamento?->num_apto ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($deudas->count() > 0)
    <!-- Deudas con seleccion -->
    <form method="POST" action="{{ route('financiero.pago-integral.procesar') }}">
        @csrf
        <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">

        <div class="card mb-6" x-data="pagoSelector()">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Deudas Pendientes ({{ $deudas->count() }})
                </h3>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-slate_custom-400">
                        <i class="fas fa-info-circle mr-1"></i>Seleccione desde la mas antigua. No puede pagar solo la mas reciente.
                    </span>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                        <span class="text-slate_custom-600 text-xs">Todas</span>
                    </label>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th class="w-10"></th>
                                <th>Periodo</th>
                                <th>Monto Original</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deudas as $index => $deuda)
                            <tr class="hover:bg-slate_custom-50 transition">
                                <td>
                                    @if($loop->last && $deudas->count() > 1)
                                    {{-- Last debt: can only select if others are selected --}}
                                    <input type="checkbox" name="deudas[]" value="{{ $deuda->id }}"
                                           x-model="selected" :value="{{ $deuda->id }}"
                                           :disabled="selected.length === 0"
                                           @change="updateTotal()"
                                           class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                    @else
                                    <input type="checkbox" name="deudas[]" value="{{ $deuda->id }}"
                                           x-model="selected" :value="{{ $deuda->id }}"
                                           @change="updateTotal()"
                                           class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                    @endif
                                </td>
                                <td class="font-medium text-navy-800">{{ $deuda->periodo }}</td>
                                <td class="text-sm">{{ number_format($deuda->monto_original ?? 0, 2, ',', '.') }} Bs</td>
                                <td class="font-semibold text-red-600">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="p-4 border-t border-slate_custom-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate_custom-500">Seleccionadas: <strong x-text="selected.length">0</strong> de {{ $deudas->count() }}</span>
                        <div class="text-right">
                            <span class="text-xs text-slate_custom-400 block">Total a Pagar</span>
                            <span class="text-2xl font-heading font-bold text-burgundy-800" x-text="formatBs(total)">0,00 Bs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary" x-bind:disabled="selected.length === 0">
                <i class="fas fa-credit-card mr-2"></i>Procesar Pago
            </button>
        </div>
    </form>

    <script>
    function pagoSelector() {
        return {
            selected: [],
            selectAll: false,
            total: 0,
            deudas: @json($deudas->map(fn($d) => ['id' => $d->id, 'saldo' => (float) $d->saldo])),
            toggleAll() {
                this.selected = this.selectAll ? this.deudas.map(d => d.id) : [];
                this.updateTotal();
            },
            updateTotal() {
                this.total = this.deudas.filter(d => this.selected.includes(d.id)).reduce((s, d) => s + d.saldo, 0);
            },
            formatBs(v) {
                return new Intl.NumberFormat('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(v) + ' Bs';
            }
        }
    }
    </script>

    @else
    <div class="card">
        <div class="card-body text-center py-12">
            <i class="fas fa-check-circle text-4xl text-green-400 mb-4 block"></i>
            <p class="text-slate_custom-400">Este afiliado no tiene deudas pendientes.</p>
        </div>
    </div>
    @endif

    @elseif(request()->has('afiliado_id'))
    <div class="card">
        <div class="card-body text-center py-12">
            <i class="fas fa-search text-4xl text-slate_custom-300 mb-4 block"></i>
            <p class="text-slate_custom-400">No se encontro el afiliado.</p>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-12">
            <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
            <p class="text-slate_custom-400">Seleccione un afiliado para consultar su saldo pendiente.</p>
        </div>
    </div>
    @endif
</x-app-layout>
