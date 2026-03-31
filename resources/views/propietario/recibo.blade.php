<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Recibo de Pago</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    Recibo Nro: {{ $pagoApto->pago?->numero_recibo ?? 'S/N' }}
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="{{ route('mi-condominio.pagos') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card print:shadow-none print:border print:border-gray-300" id="recibo-print">
            {{-- Company Header --}}
            <div class="bg-gradient-to-r from-navy-800 to-burgundy-800 text-white p-6 print:bg-white print:text-black print:border-b-2 print:border-navy-800">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 print:bg-gray-100 print:border print:border-gray-300">
                        <span class="text-2xl font-heading font-bold print:text-navy-800">AI</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-heading font-bold print:text-navy-800">
                            {{ $pagoApto->apartamento->edificio->compania->nombre ?? 'Administradora Integral' }}
                        </h2>
                        <p class="text-sm text-white/70 print:text-gray-600">
                            RIF: {{ $pagoApto->apartamento->edificio->compania->rif ?? '--' }}
                        </p>
                        <p class="text-xs text-white/60 print:text-gray-500">
                            {{ $pagoApto->apartamento->edificio->compania->direccion ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center py-4 border-b border-slate_custom-200">
                <h1 class="text-xl font-heading font-bold text-burgundy-800 uppercase tracking-wider">
                    <i class="fas fa-file-invoice-dollar mr-2 print:hidden"></i>Recibo de Pago
                </h1>
            </div>

            {{-- Details --}}
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Nro Recibo:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->pago?->numero_recibo ?? 'S/N' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Fecha:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->pago?->fecha_pago ? \Carbon\Carbon::parse($pagoApto->pago->fecha_pago)->format('d/m/Y') : '--' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Propietario:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $propietario->nombres }} {{ $propietario->apellidos }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">CI:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $propietario->cedula }}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Edificio:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->apartamento->edificio->nombre }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Apartamento:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->apartamento->num_apto }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Periodo:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->periodo }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Forma de Pago:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->pago?->forma_pago ?? '--' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Referencia:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->pago?->numero_referencia ?? '--' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Banco:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $pagoApto->pago?->banco?->nombre ?? '--' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Amount Box --}}
                <div class="bg-gradient-to-r from-navy-800 to-burgundy-800 rounded-xl p-6 text-center print:bg-white print:border-2 print:border-navy-800">
                    <p class="text-sm text-white/70 uppercase tracking-wider mb-1 print:text-gray-600">Monto Pagado</p>
                    <p class="text-3xl font-heading font-bold text-white print:text-navy-800">
                        {{ number_format($pagoApto->monto_aplicado, 2, ',', '.') }} Bs
                    </p>
                </div>

                {{-- Footer --}}
                <div class="mt-6 pt-4 border-t border-slate_custom-200 text-center">
                    <p class="text-xs text-slate_custom-400">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Este recibo es generado electronicamente y no requiere firma.
                    </p>
                    <p class="text-xs text-slate_custom-400 mt-1">
                        Fecha de impresion: {{ now()->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body * { visibility: hidden; }
            #recibo-print, #recibo-print * { visibility: visible; }
            #recibo-print { position: absolute; left: 0; top: 0; width: 100%; }
            .print\:hidden { display: none !important; }
        }
    </style>
    @endpush
</x-app-layout>
