<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Comprobante de Pago</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Comprobante de pago en l&iacute;nea #{{ $pago->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card" id="comprobante">
            <!-- Company Header -->
            <div class="card-body border-b border-slate_custom-200 text-center py-8">
                <div class="w-16 h-16 bg-navy-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
                <h3 class="text-xl font-heading font-bold text-navy-800">{{ $pago->compania->nombre ?? 'Administradora de Condominio' }}</h3>
                <p class="text-sm text-slate_custom-400">RIF: {{ $pago->compania->rif ?? 'N/A' }}</p>
                <div class="mt-4">
                    <span class="inline-block bg-burgundy-800 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full">
                        COMPROBANTE DE PAGO EN L&Iacute;NEA
                    </span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mb-8">
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">ID de Pago</span>
                        <span class="text-sm font-heading font-bold text-navy-800">#{{ $pago->id }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">Fecha</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $pago->fecha?->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">Monto Total</span>
                        <span class="text-lg font-heading font-bold text-burgundy-800">{{ number_format($pago->monto_total, 2, ',', '.') }} Bs</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">Forma de Pago</span>
                        <span class="text-sm font-semibold text-navy-800">{{ ucfirst(str_replace('_', ' ', $pago->forma_pago)) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">Referencia</span>
                        <span class="text-sm font-semibold text-navy-800">{{ $pago->referencia }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate_custom-400 block uppercase tracking-wider">Estatus</span>
                        @if($pago->estatus === 'P')
                            <span class="badge-warning">Pendiente</span>
                        @elseif($pago->estatus === 'A')
                            <span class="badge-success">Aprobado</span>
                        @elseif($pago->estatus === 'R')
                            <span class="badge-danger">Rechazado</span>
                        @else
                            <span class="badge-info">{{ $pago->estatus }}</span>
                        @endif
                    </div>
                </div>

                @if($pago->observaciones)
                <div class="bg-slate_custom-50 rounded-lg p-3 mb-6">
                    <span class="text-xs text-slate_custom-400 block">Observaciones</span>
                    <span class="text-sm text-navy-800">{{ $pago->observaciones }}</span>
                </div>
                @endif

                <!-- Detail Table -->
                @if($pago->pagoIntegralDetalles && $pago->pagoIntegralDetalles->count() > 0)
                <div class="border-t border-slate_custom-200 pt-6">
                    <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4">
                        <i class="fas fa-list-ul mr-2 text-burgundy-800"></i>Detalle de Periodos Pagados
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Periodo</th>
                                    <th>Concepto</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pago->pagoIntegralDetalles as $index => $detalle)
                                <tr>
                                    <td class="text-slate_custom-400">{{ $index + 1 }}</td>
                                    <td class="font-medium">{{ $detalle->periodo }}</td>
                                    <td>{{ $detalle->concepto ?? 'Cuota de Condominio' }}</td>
                                    <td class="text-right font-semibold">{{ number_format($detalle->monto, 2, ',', '.') }} Bs</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-50">
                                    <td colspan="3" class="text-right font-heading font-bold text-navy-800">TOTAL:</td>
                                    <td class="text-right text-lg font-heading font-bold text-burgundy-800">{{ number_format($pago->pagoIntegralDetalles->sum('monto'), 2, ',', '.') }} Bs</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-dashed border-slate_custom-300 text-center">
                    <p class="text-xs text-slate_custom-400">Este comprobante es un documento informativo generado electr&oacute;nicamente.</p>
                    <p class="text-xs text-slate_custom-400">Fecha de generaci&oacute;n: {{ now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center gap-4 mt-6">
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print mr-2"></i>Imprimir Comprobante
            </button>
            <a href="{{ route('financiero.pago-integral.index') }}" class="btn-secondary">
                <i class="fas fa-home mr-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
</x-app-layout>
