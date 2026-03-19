<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Consultar Saldo</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Consulte deudas pendientes y seleccione las que desea pagar</p>
            </div>
            <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <!-- Filter Form -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-search-dollar mr-2 text-burgundy-800"></i>Buscar Apartamento
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('financiero.pago-integral.consultar-saldo') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Edificio</label>
                        <select name="edificio_id" id="saldo_edificio_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccione Edificio --</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ request('edificio_id') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate_custom-600 mb-1">Apartamento</label>
                        <select name="apartamento_id" id="saldo_apartamento_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccione Apartamento --</option>
                            @foreach($apartamentos as $apto)
                                <option value="{{ $apto->id }}" {{ request('apartamento_id') == $apto->id ? 'selected' : '' }}>
                                    {{ $apto->num_apto }} - {{ $apto->propietario_nombre }}
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
        </div>
    </div>

    <!-- Filter apartments by edificio -->
    <script>
        document.getElementById('saldo_edificio_id').addEventListener('change', function() {
            const edificioId = this.value;
            const aptoSelect = document.getElementById('saldo_apartamento_id');
            const allOptions = @json($apartamentos->map(fn($a) => ['id' => $a->id, 'edificio_id' => $a->edificio_id, 'label' => $a->num_apto . ' - ' . $a->propietario_nombre]));

            aptoSelect.innerHTML = '<option value="">-- Seleccione Apartamento --</option>';
            allOptions.forEach(function(opt) {
                if (!edificioId || opt.edificio_id == edificioId) {
                    const option = document.createElement('option');
                    option.value = opt.id;
                    option.textContent = opt.label;
                    aptoSelect.appendChild(option);
                }
            });
        });
    </script>

    @if($apartamento)
        <!-- Apartment Info Card -->
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-building mr-2 text-burgundy-800"></i>Datos del Apartamento
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Edificio</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->edificio->nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Apartamento</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->num_apto }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Propietario</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $apartamento->propietario_nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block">Al&iacute;cuota</span>
                        <span class="text-sm font-semibold text-navy-800">{{ number_format($apartamento->alicuota, 4) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($deudas->count() > 0)
            <!-- Deudas Pendientes Table with Checkboxes -->
            <form method="POST" action="{{ route('financiero.pago-integral.procesar') }}" id="pagoForm">
                @csrf
                <input type="hidden" name="apartamento_id" value="{{ $apartamento->id }}">

                <div class="card mb-6" x-data="pagoSelector()">
                    <div class="card-header flex items-center justify-between">
                        <h3 class="text-sm font-heading font-semibold text-navy-800">
                            <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Deudas Pendientes
                        </h3>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                            <span class="text-slate_custom-600">Seleccionar todas</span>
                        </label>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th class="w-10"></th>
                                        <th>Periodo</th>
                                        <th>Monto Original</th>
                                        <th>Mora</th>
                                        <th>Pagado</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deudas as $index => $deuda)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   name="deudas[]"
                                                   value="{{ $deuda->id }}"
                                                   x-model="selected"
                                                   :value="{{ $deuda->id }}"
                                                   data-monto="{{ $deuda->saldo }}"
                                                   @change="updateTotal()"
                                                   class="rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                        </td>
                                        <td class="font-medium">{{ $deuda->periodo }}</td>
                                        <td>{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                        <td>{{ number_format($deuda->monto_mora, 2, ',', '.') }} Bs</td>
                                        <td>{{ number_format($deuda->monto_pagado, 2, ',', '.') }} Bs</td>
                                        <td class="font-semibold text-red-600">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Selected -->
                        <div class="mt-6 border-t border-slate_custom-200 pt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm text-slate_custom-500">Deudas seleccionadas: <strong x-text="selected.length">0</strong></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-slate_custom-500 block">Total a Pagar</span>
                                    <span class="text-2xl font-heading font-bold text-burgundy-800" x-text="formatBs(total)">0,00 Bs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Procesar Pago Button -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary" id="btnProcesar">
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
                            if (this.selectAll) {
                                this.selected = this.deudas.map(d => d.id);
                            } else {
                                this.selected = [];
                            }
                            this.updateTotal();
                        },
                        updateTotal() {
                            this.total = this.deudas
                                .filter(d => this.selected.includes(d.id))
                                .reduce((sum, d) => sum + d.saldo, 0);
                        },
                        formatBs(value) {
                            return new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' Bs';
                        }
                    }
                }
            </script>
        @else
            <div class="card">
                <div class="card-body text-center py-12">
                    <i class="fas fa-check-circle text-4xl text-green-400 mb-4 block"></i>
                    <p class="text-slate_custom-400">Este apartamento no tiene deudas pendientes.</p>
                </div>
            </div>
        @endif
    @elseif(request()->has('apartamento_id'))
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-search text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">No se encontr&oacute; el apartamento seleccionado.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <i class="fas fa-hand-pointer text-4xl text-slate_custom-300 mb-4 block"></i>
                <p class="text-slate_custom-400">Seleccione un edificio y apartamento para consultar el saldo pendiente.</p>
            </div>
        </div>
    @endif
</x-app-layout>
