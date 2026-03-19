<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Recibo de Condominio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    Periodo: {{ $deuda->periodo }}
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="{{ route('mi-condominio.deudas') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card print:shadow-none print:border print:border-gray-300" id="recibo-condominio-print">
            {{-- Company Header --}}
            <div class="bg-gradient-to-r from-navy-800 to-burgundy-800 text-white p-6 print:bg-white print:text-black print:border-b-2 print:border-navy-800">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 print:bg-gray-100 print:border print:border-gray-300">
                        <span class="text-2xl font-heading font-bold print:text-navy-800">AI</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-heading font-bold print:text-navy-800">
                            {{ $deuda->apartamento->edificio->compania->nombre ?? 'Administradora Integral' }}
                        </h2>
                        <p class="text-sm text-white/70 print:text-gray-600">
                            RIF: {{ $deuda->apartamento->edificio->compania->rif ?? '--' }}
                        </p>
                        <p class="text-xs text-white/60 print:text-gray-500">
                            {{ $deuda->apartamento->edificio->compania->direccion ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center py-4 border-b border-slate_custom-200">
                <h1 class="text-xl font-heading font-bold text-burgundy-800 uppercase tracking-wider">
                    <i class="fas fa-building mr-2 print:hidden"></i>Recibo de Condominio
                </h1>
            </div>

            {{-- Details --}}
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Periodo:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $deuda->periodo }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Edificio:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $deuda->apartamento->edificio->nombre }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Apartamento:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $deuda->apartamento->num_apto }}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Propietario:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $propietario->nombres }} {{ $propietario->apellidos }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">CI:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $propietario->cedula }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Fecha Emision:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $deuda->fecha_emision ? \Carbon\Carbon::parse($deuda->fecha_emision)->format('d/m/Y') : '--' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate_custom-100">
                            <span class="text-sm text-slate_custom-500 font-medium">Fecha Vencimiento:</span>
                            <span class="text-sm font-semibold text-navy-800">{{ $deuda->fecha_vencimiento ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)->format('d/m/Y') : '--' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Breakdown Table --}}
                <div class="bg-slate_custom-100 rounded-xl p-4 mb-6">
                    <h3 class="text-sm font-heading font-semibold text-navy-800 mb-3">
                        <i class="fas fa-calculator mr-2 text-burgundy-800"></i>Desglose
                    </h3>
                    <table class="w-full">
                        <tbody>
                            <tr class="border-b border-slate_custom-200">
                                <td class="py-2 text-sm text-slate_custom-500">Monto Original</td>
                                <td class="py-2 text-sm font-semibold text-navy-800 text-right">{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                            </tr>
                            <tr class="border-b border-slate_custom-200">
                                <td class="py-2 text-sm text-slate_custom-500">Mora</td>
                                <td class="py-2 text-sm font-semibold text-red-600 text-right">+ {{ number_format($deuda->monto_mora ?? 0, 2, ',', '.') }} Bs</td>
                            </tr>
                            <tr class="border-b border-slate_custom-200">
                                <td class="py-2 text-sm text-slate_custom-500">Intereses</td>
                                <td class="py-2 text-sm font-semibold text-red-600 text-right">+ {{ number_format($deuda->monto_interes ?? 0, 2, ',', '.') }} Bs</td>
                            </tr>
                            <tr class="border-b border-slate_custom-200">
                                <td class="py-2 text-sm text-slate_custom-500">Descuento</td>
                                <td class="py-2 text-sm font-semibold text-green-600 text-right">- {{ number_format($deuda->monto_descuento ?? 0, 2, ',', '.') }} Bs</td>
                            </tr>
                            @php
                                $total = ($deuda->monto_original ?? 0) + ($deuda->monto_mora ?? 0) + ($deuda->monto_interes ?? 0) - ($deuda->monto_descuento ?? 0);
                            @endphp
                            <tr class="border-b-2 border-navy-800">
                                <td class="py-3 text-sm font-bold text-navy-800">Total</td>
                                <td class="py-3 text-sm font-bold text-navy-800 text-right">{{ number_format($total, 2, ',', '.') }} Bs</td>
                            </tr>
                            <tr class="border-b border-slate_custom-200">
                                <td class="py-2 text-sm text-slate_custom-500">Pagado</td>
                                <td class="py-2 text-sm font-semibold text-green-600 text-right">{{ number_format($deuda->monto_pagado ?? 0, 2, ',', '.') }} Bs</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-sm font-bold text-burgundy-800">Saldo Pendiente</td>
                                <td class="py-3 text-lg font-bold text-burgundy-800 text-right">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Status Badge --}}
                <div class="text-center mb-6">
                    @if($deuda->estatus === 'P')
                        <span class="inline-flex items-center px-6 py-2 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 border border-yellow-300">
                            <i class="fas fa-clock mr-2"></i>Pendiente de Pago
                        </span>
                    @elseif($deuda->estatus === 'C')
                        <span class="inline-flex items-center px-6 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-300">
                            <i class="fas fa-check-circle mr-2"></i>Cancelada
                        </span>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="pt-4 border-t border-slate_custom-200 text-center">
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
            #recibo-condominio-print, #recibo-condominio-print * { visibility: visible; }
            #recibo-condominio-print { position: absolute; left: 0; top: 0; width: 100%; }
            .print\:hidden { display: none !important; }
        }
    </style>
    @endpush
</x-app-layout>
