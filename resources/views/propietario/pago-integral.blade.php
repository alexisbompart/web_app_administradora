<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Pago Integral</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    <i class="fas fa-user mr-1"></i>{{ $propietario->nombres }} {{ $propietario->apellidos }} - CI: {{ $propietario->cedula }}
                </p>
            </div>
            <a href="{{ route('mi-condominio.dashboard') }}" class="btn-secondary">
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

    @if(!$afiliado)
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-slash text-2xl text-amber-500"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">Sin afiliacion activa</h3>
                <p class="text-sm text-slate_custom-400">No tiene una afiliacion activa para Pago Integral. Contacte al administrador.</p>
            </div>
        </div>
    @elseif($deudas->isEmpty())
        {{-- Afiliado info --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-id-card mr-2 text-burgundy-800"></i>Datos del Afiliado
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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

        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No tiene deudas pendientes</h3>
                <p class="text-sm text-slate_custom-400">Todos sus recibos estan al dia.</p>
            </div>
        </div>
    @else
        {{-- Afiliado info --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-id-card mr-2 text-burgundy-800"></i>Datos del Afiliado
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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

        {{-- Formulario de seleccion de deudas --}}
        <form method="POST" action="{{ route('mi-condominio.pago-integral.store') }}" id="form-pago-integral">
            @csrf
            <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">

            <div class="card mb-6">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-file-invoice-dollar mr-2 text-burgundy-800"></i>Seleccione los meses a pagar ({{ $deudas->count() }} pendientes)
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
                                    <th>Periodo</th>
                                    <th>Apartamento</th>
                                    <th class="text-right">Monto Original</th>
                                    <th class="text-right">Saldo Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deudas as $deuda)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="deudas[]" value="{{ $deuda->id }}"
                                               data-saldo="{{ number_format((float)$deuda->saldo, 2, '.', '') }}"
                                               class="check-deuda rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800">
                                    </td>
                                    <td class="font-medium text-navy-800">{{ $deuda->periodo }}</td>
                                    <td>{{ $afiliado->afilapto?->apartamento?->edificio?->nombre }} - {{ $afiliado->afilapto?->apartamento?->num_apto }}</td>
                                    <td class="text-right">{{ number_format($deuda->monto_original ?? 0, 2, ',', '.') }} Bs</td>
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
                            <span id="count-seleccionados">0</span> mes(es) seleccionado(s)
                        </p>
                        <p class="text-lg font-heading font-bold text-burgundy-800">
                            Total: <span id="total-seleccionado">0,00</span> Bs
                        </p>
                    </div>
                </div>
            </div>

            {{-- Alerta de orden --}}
            <div id="alerta-orden" class="bg-amber-50 border border-amber-300 rounded-xl p-4 mb-6 flex items-start gap-3" style="display: none;">
                <i class="fas fa-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
                <div>
                    <p class="font-heading font-bold text-amber-800">Debe pagar las deudas en orden</p>
                    <p class="text-sm text-amber-700 mt-1">Las deudas deben pagarse desde la mas antigua hasta la mas reciente, sin saltar periodos. Seleccione primero los meses mas antiguos.</p>
                </div>
            </div>

            {{-- Resumen y confirmacion --}}
            <div id="paso-confirmar" class="card mb-6" style="display: none;">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-receipt mr-2 text-burgundy-800"></i>Resumen del Pago
                    </h3>
                </div>
                <div class="card-body">
                    <div class="bg-slate_custom-50 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate_custom-200">
                            <span class="text-sm text-slate_custom-500">Meses seleccionados:</span>
                            <span class="text-sm font-semibold text-navy-800" id="resumen-count">0</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-base font-bold text-navy-800">Monto Total:</span>
                            <span class="text-xl font-heading font-bold text-burgundy-800" id="resumen-total">0,00 Bs</span>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
                            <p class="text-sm text-blue-700">
                                Su solicitud de pago quedara en estatus <strong>Pendiente</strong> hasta que el administrador la apruebe.
                                No necesita ingresar datos bancarios, el cobro se procesara automaticamente a traves del sistema de pago integral.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate_custom-200">
                        <a href="{{ route('mi-condominio.dashboard') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="button" id="btn-enviar" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Solicitar Pago
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif

    {{-- Historial de pagos integrales --}}
    @if(isset($pagosIntegrales) && $pagosIntegrales->isNotEmpty())
    <div class="card mt-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-history mr-2 text-burgundy-800"></i>Mis Pagos Integrales
            </h3>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Periodos</th>
                            <th class="text-right">Monto</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagosIntegrales as $pago)
                        <tr>
                            <td class="font-medium">{{ $pago->id }}</td>
                            <td>{{ $pago->fecha?->format('d/m/Y') }}</td>
                            <td>
                                @foreach($pago->pagoIntegralDetalles as $det)
                                    <span class="text-xs bg-slate_custom-100 text-navy-800 px-2 py-0.5 rounded-full">{{ $det->periodo }}</span>
                                @endforeach
                            </td>
                            <td class="text-right font-semibold">{{ number_format($pago->monto_total, 2, ',', '.') }} Bs</td>
                            <td>
                                @if($pago->estatus === 'P')
                                    <span class="badge-warning">Pendiente</span>
                                @elseif($pago->estatus === 'A')
                                    <span class="badge-success">Aprobado</span>
                                @elseif($pago->estatus === 'R')
                                    <span class="badge-danger">Rechazado</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($deudas) && $deudas->isNotEmpty())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkboxes = document.querySelectorAll('.check-deuda');
        var countEl = document.getElementById('count-seleccionados');
        var totalEl = document.getElementById('total-seleccionado');
        var resumenCount = document.getElementById('resumen-count');
        var resumenTotal = document.getElementById('resumen-total');
        var pasoConfirmar = document.getElementById('paso-confirmar');
        var btnTodos = document.getElementById('btn-todos');
        var btnNinguno = document.getElementById('btn-ninguno');
        var btnEnviar = document.getElementById('btn-enviar');
        var form = document.getElementById('form-pago-integral');
        var alertaOrden = document.getElementById('alerta-orden');

        function formatMoney(val) {
            return val.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function validarOrden() {
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

        function validarOrdenAlSeleccionar(indexActual) {
            for (var i = 0; i < indexActual; i++) {
                if (!checkboxes[i].checked) return false;
            }
            return true;
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

            if (count > 0 && !validarOrden()) {
                alertaOrden.style.display = '';
                pasoConfirmar.style.display = 'none';
            } else if (count > 0) {
                alertaOrden.style.display = 'none';
                pasoConfirmar.style.display = '';
            } else {
                alertaOrden.style.display = 'none';
                pasoConfirmar.style.display = 'none';
            }
        }

        checkboxes.forEach(function(cb, index) {
            cb.addEventListener('change', function() {
                if (this.checked && !validarOrdenAlSeleccionar(index)) {
                    this.checked = false;
                    alert('Debe pagar las deudas mas antiguas primero.\n\nSeleccione los meses en orden, comenzando por el mas antiguo.');
                    return;
                }
                recalcular();
            });
        });

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
                alert('Debe seleccionar al menos un mes para pagar.');
                return;
            }

            if (!validarOrden()) {
                alert('Debe pagar las deudas en orden cronologico, desde la mas antigua.');
                return;
            }

            if (confirm('Confirma solicitar el pago integral por ' + resumenTotal.textContent + '?\n\nEl pago quedara pendiente de aprobacion.')) {
                form.submit();
            }
        });
    });
    </script>
    @endif
</x-app-layout>
