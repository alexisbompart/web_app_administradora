<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Registrar Pago</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-user mr-1"></i>{{ $propietario->nombres }} {{ $propietario->apellidos }} - CI: {{ $propietario->cedula }}
                </p>
            </div>
            <a href="{{ route('mi-condominio.deudas') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($deudasPendientes->isEmpty())
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No tiene deudas pendientes</h3>
                <p class="text-sm text-slate_custom-400">Todos sus recibos estan al dia.</p>
            </div>
        </div>
    @elseif($deudasPendientes->count() > 4)
        {{-- COBRANZA EXTRAJUDICIAL --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="bg-red-50 border-2 border-red-400 rounded-2xl p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-gavel text-red-600 text-3xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-heading font-bold text-red-700 mb-3">COBRANZA EXTRAJUDICIAL</h3>
                            <p class="text-sm text-red-700 leading-relaxed">
                                Apreciado cliente, la deuda relativa a este apartamento, se encuentra en cobranza extrajudicial
                                debido al atraso de pago, que a la fecha debe <strong>{{ $deudasPendientes->count() }} meses</strong>.
                                S&iacute;rvase comunicarse al Dpto. Cobranzas a la direcci&oacute;n e-mail:
                                <a href="mailto:cobranzasintegralelb@gmail.com" class="underline font-bold">cobranzasintegralelb@gmail.com</a>
                                o al tel&eacute;fono <strong>0212-9515611 Ext. 413</strong>.
                            </p>
                            <div class="mt-5 flex flex-col sm:flex-row gap-3">
                                <a href="mailto:cobranzasintegralelb@gmail.com" class="inline-flex items-center gap-2 px-5 py-3 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-heading font-bold rounded-xl transition">
                                    <i class="fas fa-envelope"></i> cobranzasintegralelb@gmail.com
                                </a>
                                <span class="inline-flex items-center gap-2 px-5 py-3 bg-red-100 text-red-700 text-sm font-heading font-bold rounded-xl">
                                    <i class="fas fa-phone"></i> 0212-9515611 Ext. 413
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('mi-condominio.registrar-pago.store') }}" id="form-pago">
            @csrf

            <!-- Paso 1: Seleccionar recibos -->
            <div class="card mb-6">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Paso 1: Seleccione los recibos a pagar
                    </h3>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-todos" class="btn-secondary text-xs">
                            <i class="fas fa-check-double mr-1"></i>Todos
                        </button>
                        <button type="button" id="btn-ninguno" class="btn-secondary text-xs">
                            <i class="fas fa-times mr-1"></i>Ninguno
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th class="w-10"></th>
                                    <th>Apartamento</th>
                                    <th>Periodo</th>
                                    <th>Fecha Vencimiento</th>
                                    <th class="text-right">Monto Original</th>
                                    <th class="text-right">Saldo Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deudasPendientes as $deuda)
                                    @php
                                        $vencida = $deuda->fecha_vencimiento && \Carbon\Carbon::parse($deuda->fecha_vencimiento)->lt(now());
                                    @endphp
                                    <tr class="{{ $vencida ? 'bg-red-50/50' : '' }}">
                                        <td>
                                            <input type="checkbox" name="deudas[]" value="{{ $deuda->id }}"
                                                   data-saldo="{{ number_format((float)$deuda->saldo, 2, '.', '') }}"
                                                   class="check-deuda rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                        </td>
                                        <td class="font-medium text-navy-800">
                                            {{ $deuda->apartamento->edificio->nombre }} - {{ $deuda->apartamento->num_apto }}
                                        </td>
                                        <td>{{ $deuda->periodo }}</td>
                                        <td>
                                            {{ $deuda->fecha_vencimiento ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)->format('d/m/Y') : '--' }}
                                            @if($vencida)
                                                <span class="text-xs text-red-500 block"><i class="fas fa-clock mr-1"></i>Vencida</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                        <td class="text-right font-semibold text-burgundy-800">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body border-t border-slate_custom-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate_custom-500">
                            <span id="count-seleccionados">0</span> recibo(s) seleccionado(s)
                        </p>
                        <p class="text-lg font-heading font-bold text-burgundy-800">
                            Total: <span id="total-seleccionado">0,00</span> Bs
                        </p>
                    </div>
                </div>
            </div>

            <!-- Alerta de orden -->
            <div id="alerta-orden" class="bg-amber-50 border border-amber-300 rounded-xl p-4 mb-6 flex items-start gap-3" style="display: none;">
                <i class="fas fa-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
                <div>
                    <p class="font-heading font-bold text-amber-800">Debe pagar las deudas en orden</p>
                    <p class="text-sm text-amber-700 mt-1">Las deudas deben pagarse desde la mas antigua hasta la mas reciente, sin saltar periodos. Seleccione primero los recibos mas antiguos (los primeros de la lista).</p>
                </div>
            </div>

            <!-- Paso 2: Datos del pago -->
            <div class="card mb-6" id="paso2" style="display: none;">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-university mr-2 text-burgundy-800"></i>Paso 2: Datos de la transferencia / deposito
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="forma_pago" class="block text-sm font-medium text-navy-800 mb-1">Forma de Pago <span class="text-red-500">*</span></label>
                            <select name="forma_pago" id="forma_pago"
                                    class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="deposito">Deposito Bancario</option>
                            </select>
                            @error('forma_pago')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="banco_id" class="block text-sm font-medium text-navy-800 mb-1">Banco <span class="text-red-500">*</span></label>
                            <select name="banco_id" id="banco_id"
                                    class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                <option value="">Seleccione el banco</option>
                                @foreach($bancos as $banco)
                                    <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>
                                @endforeach
                            </select>
                            @error('banco_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_pago" class="block text-sm font-medium text-navy-800 mb-1">Fecha del Pago <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_pago" id="fecha_pago"
                                   value="{{ now()->format('Y-m-d') }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            @error('fecha_pago')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="numero_referencia" class="block text-sm font-medium text-navy-800 mb-1">Nro. de Referencia <span class="text-red-500">*</span></label>
                            <input type="text" name="numero_referencia" id="numero_referencia"
                                   value=""
                                   placeholder="Numero de confirmacion o referencia"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            @error('numero_referencia')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="mt-6 bg-slate_custom-50 rounded-xl p-4">
                        <h4 class="text-sm font-heading font-semibold text-navy-800 mb-3">
                            <i class="fas fa-receipt mr-2 text-burgundy-800"></i>Resumen del Pago
                        </h4>
                        <div class="flex items-center justify-between py-2 border-b border-slate_custom-200">
                            <span class="text-sm text-slate_custom-500">Recibos seleccionados:</span>
                            <span class="text-sm font-semibold text-navy-800" id="resumen-count">0</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-base font-bold text-navy-800">Monto Total a Pagar:</span>
                            <span class="text-xl font-heading font-bold text-burgundy-800" id="resumen-total">0,00 Bs</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                            <p class="text-sm text-blue-700">
                                Su pago quedara en estatus <strong>Pendiente</strong> hasta que el administrador lo verifique y apruebe.
                                Una vez aprobado, se aplicara automaticamente a los recibos seleccionados.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                        <a href="{{ route('mi-condominio.deudas') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="button" id="btn-enviar" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Registrar Pago
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var checkboxes = document.querySelectorAll('.check-deuda');
            var countEl = document.getElementById('count-seleccionados');
            var totalEl = document.getElementById('total-seleccionado');
            var resumenCount = document.getElementById('resumen-count');
            var resumenTotal = document.getElementById('resumen-total');
            var paso2 = document.getElementById('paso2');
            var btnTodos = document.getElementById('btn-todos');
            var btnNinguno = document.getElementById('btn-ninguno');
            var btnEnviar = document.getElementById('btn-enviar');
            var form = document.getElementById('form-pago');
            var alertaOrden = document.getElementById('alerta-orden');

            function formatMoney(val) {
                return val.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function validarOrden() {
                // Check that selection is consecutive from the oldest (first)
                var primerNoSeleccionado = false;
                var hayHueco = false;

                checkboxes.forEach(function(cb) {
                    if (!cb.checked) {
                        primerNoSeleccionado = true;
                    } else if (primerNoSeleccionado) {
                        hayHueco = true;
                    }
                });

                return !hayHueco;
            }

            function recalcular() {
                var total = 0;
                var count = 0;

                checkboxes.forEach(function(cb) {
                    if (cb.checked) {
                        total += parseFloat(cb.getAttribute('data-saldo')) || 0;
                        count++;
                    }
                });

                countEl.textContent = count;
                totalEl.textContent = formatMoney(total);
                resumenCount.textContent = count;
                resumenTotal.textContent = formatMoney(total) + ' Bs';

                // Validate order
                if (count > 0 && !validarOrden()) {
                    alertaOrden.style.display = '';
                    paso2.style.display = 'none';
                } else if (count > 0) {
                    alertaOrden.style.display = 'none';
                    paso2.style.display = '';
                } else {
                    alertaOrden.style.display = 'none';
                    paso2.style.display = 'none';
                }
            }

            checkboxes.forEach(function(cb, index) {
                cb.addEventListener('change', function() {
                    if (this.checked && !validarOrdenAlSeleccionar(index)) {
                        this.checked = false;
                        alert('Debe pagar las deudas mas antiguas primero.\n\nSeleccione los recibos en orden, comenzando por el mas antiguo (el primero de la lista).');
                        return;
                    }
                    recalcular();
                });
            });

            function validarOrdenAlSeleccionar(indexActual) {
                // When checking a box, all previous must be checked
                for (var i = 0; i < indexActual; i++) {
                    if (!checkboxes[i].checked) {
                        return false;
                    }
                }
                return true;
            }

            btnTodos.addEventListener('click', function() {
                checkboxes.forEach(function(cb) { cb.checked = true; });
                recalcular();
            });

            btnNinguno.addEventListener('click', function() {
                checkboxes.forEach(function(cb) { cb.checked = false; });
                recalcular();
            });

            btnEnviar.addEventListener('click', function() {
                var count = 0;
                checkboxes.forEach(function(cb) { if (cb.checked) count++; });

                if (count === 0) {
                    alert('Debe seleccionar al menos un recibo para pagar.');
                    return;
                }

                if (!validarOrden()) {
                    alert('Debe pagar las deudas en orden cronologico, desde la mas antigua.');
                    return;
                }

                var banco = document.getElementById('banco_id').value;
                if (!banco) {
                    alert('Debe seleccionar un banco.');
                    return;
                }

                var referencia = document.getElementById('numero_referencia').value.trim();
                if (!referencia) {
                    alert('Debe ingresar el numero de referencia.');
                    return;
                }

                if (confirm('¿Esta seguro de registrar este pago por ' + resumenTotal.textContent + '?')) {
                    form.submit();
                }
            });
        });
        </script>
    @endif
</x-app-layout>
