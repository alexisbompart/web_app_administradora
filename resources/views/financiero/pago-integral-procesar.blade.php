<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Procesar Pago en L&iacute;nea</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Confirme y procese el pago de las deudas seleccionadas</p>
            </div>
            <a href="{{ route('financiero.pago-integral.consultar-saldo') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Summary of Debts -->
        <div class="lg:col-span-2">
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-sm font-heading font-semibold text-navy-800">
                        <i class="fas fa-file-invoice mr-2 text-burgundy-800"></i>Resumen de Deudas a Pagar
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Afiliado Info -->
                    <div class="bg-slate_custom-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <span class="text-xs text-slate_custom-400 block">Afiliado</span>
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

                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Concepto</th>
                                    <th class="text-right">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deudas as $deuda)
                                <tr>
                                    <td class="font-medium">{{ $deuda->periodo }}</td>
                                    <td>Cuota de Condominio</td>
                                    <td class="text-right font-semibold">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50">
                                    <td colspan="2" class="text-right font-heading font-bold text-navy-800">TOTAL A PAGAR:</td>
                                    <td class="text-right text-xl font-heading font-bold text-burgundy-800">{{ number_format($total, 2, ',', '.') }} Bs</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Payment Form -->
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('financiero.pago-integral.procesar') }}" id="procesarPagoForm">
                @csrf
                <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                @foreach($deudas as $deuda)
                    <input type="hidden" name="deudas[]" value="{{ $deuda->id }}">
                @endforeach
                <input type="hidden" name="confirmar" value="1">

                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="text-sm font-heading font-semibold text-navy-800">
                            <i class="fas fa-credit-card mr-2 text-burgundy-800"></i>Forma de Pago
                        </h3>
                    </div>
                    <div class="card-body space-y-4">
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-slate_custom-600 mb-1">M&eacute;todo de Pago</label>
                            <select name="forma_pago" required class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                                <option value="">-- Seleccione --</option>
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="debito_banesco">D&eacute;bito - Banesco</option>
                                <option value="debito_mercantil">D&eacute;bito - Mercantil</option>
                                <option value="debito_bancaribe">D&eacute;bito - Bancaribe</option>
                            </select>
                        </div>

                        <!-- Bank Account Info (Simulated) -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-xs font-semibold text-blue-800 uppercase mb-2">
                                <i class="fas fa-info-circle mr-1"></i>Datos Bancarios
                            </h4>
                            <div class="space-y-1 text-xs text-blue-700">
                                <p><strong>Banco:</strong> Banesco</p>
                                <p><strong>Cuenta:</strong> 0134-0000-00-0000000000</p>
                                <p><strong>Titular:</strong> Administradora Condominio C.A.</p>
                                <p><strong>RIF:</strong> J-00000000-0</p>
                            </div>
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label class="block text-sm font-medium text-slate_custom-600 mb-1">N&uacute;mero de Referencia</label>
                            <input type="text" name="referencia" required maxlength="50" placeholder="Ej: 0012345678" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        </div>

                        <!-- Observations -->
                        <div>
                            <label class="block text-sm font-medium text-slate_custom-600 mb-1">Observaciones (opcional)</label>
                            <textarea name="observaciones" rows="2" placeholder="Notas adicionales..." class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"></textarea>
                        </div>

                        <!-- Total -->
                        <div class="bg-burgundy-800/5 rounded-lg p-4 text-center">
                            <span class="text-xs text-slate_custom-500 block">Monto Total</span>
                            <span class="text-2xl font-heading font-bold text-burgundy-800">{{ number_format($total, 2, ',', '.') }} Bs</span>
                        </div>

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <ul class="text-xs text-red-600 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Confirm Button -->
                        <button type="submit" class="btn-primary w-full" onclick="return confirm('Confirma que desea procesar este pago por {{ number_format($total, 2, ',', '.') }} Bs?')">
                            <i class="fas fa-lock mr-2"></i>Confirmar y Pagar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
