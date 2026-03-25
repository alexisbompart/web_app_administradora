<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Recibo Edificio - {{ $factEdif->edificio?->nombre }}</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Periodo {{ $factEdif->periodo }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="btn-primary"><i class="fas fa-print mr-2"></i>Imprimir</button>
                <a href="{{ route('mi-condominio.recibos-edificio') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
            </div>
        </div>
    </x-slot>

    @php
        $edificio = $factEdif->edificio;
        $compania = $edificio?->compania;
    @endphp

    <div class="max-w-5xl mx-auto print:max-w-full" id="recibo-print">
        {{-- HEADER --}}
        <div class="card mb-4 print:shadow-none print:border-2 print:border-black">
            <div class="card-body p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL' }}</h3>
                        <p class="text-xs text-slate_custom-500">{{ $compania?->rif }}</p>
                        <p class="text-xs text-slate_custom-500">{{ $compania?->direccion }}</p>
                    </div>
                    <div class="text-center">
                        <h2 class="font-heading font-bold text-xl text-burgundy-800">RELACION MENSUAL<br>DEL CONDOMINIO</h2>
                        <p class="text-sm font-semibold text-navy-800 mt-1">RESUMEN EDIFICIO</p>
                    </div>
                    <div class="text-right bg-slate_custom-50 rounded-lg p-3">
                        <p class="text-xs text-slate_custom-500">Edificio</p>
                        <p class="text-sm font-bold text-navy-800">{{ $edificio?->nombre }}</p>
                        <p class="text-xs text-slate_custom-500 mt-1">Cod. {{ $edificio?->cod_edif }}</p>
                        <p class="text-xs text-slate_custom-500">Periodo: {{ $factEdif->periodo }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            {{-- FONDOS Y DEUDA (2 cols) --}}
            <div class="lg:col-span-2">
                <div class="card h-full print:shadow-none print:border print:border-black">
                    {{-- Fondos --}}
                    <div class="bg-burgundy-800 px-3 py-2">
                        <h4 class="text-xs font-bold text-white uppercase">Resumen de Fondos y Deuda</h4>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-6">
                            {{-- Fondo Reserva --}}
                            <div>
                                <h5 class="font-bold text-navy-800 text-sm text-center bg-slate_custom-100 rounded py-1.5 mb-3">FONDO DE RESERVA</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Saldo Anterior</span><span class="font-semibold">{{ number_format($factEdif->sdo_ant_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Menos Cargos</span><span>{{ number_format($factEdif->cargos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Mas Abonos</span><span>{{ number_format($factEdif->abonos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t pt-2"><span class="font-bold">Saldo Actual</span><span class="font-bold text-burgundy-800">{{ number_format($factEdif->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Intereses Acum.</span><span>{{ number_format($factEdif->int_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                </div>
                            </div>
                            {{-- Deuda Comun --}}
                            <div>
                                <h5 class="font-bold text-navy-800 text-sm text-center bg-slate_custom-100 rounded py-1.5 mb-3">DEUDA COMUN</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Saldo Anterior</span><span class="font-semibold">{{ number_format($factEdif->deuda_ant_edif ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Menos Cobranza</span><span>{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Mas Facturacion</span><span>{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t pt-2"><span class="font-bold">Saldo Actual</span><span class="font-bold text-red-600">{{ number_format($factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span class="text-slate_custom-500">Recibos Pendientes</span><span class="font-bold">{{ $factEdif->recibos_pend ?? 0 }}</span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Otros fondos --}}
                        <div class="mt-4 pt-4 border-t">
                            <h5 class="font-bold text-navy-800 text-xs mb-2">OTROS FONDOS</h5>
                            <div class="grid grid-cols-3 gap-4 text-xs">
                                <div class="bg-slate_custom-50 rounded p-2 text-center">
                                    <span class="text-slate_custom-400 block">Fdo. Especial</span>
                                    <span class="font-semibold">{{ number_format($factEdif->sdo_act_fdo_esp ?? 0, 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-slate_custom-50 rounded p-2 text-center">
                                    <span class="text-slate_custom-400 block">Fdo. Social</span>
                                    <span class="font-semibold">{{ number_format($factEdif->sdo_act_fdo_soc ?? 0, 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-slate_custom-50 rounded p-2 text-center">
                                    <span class="text-slate_custom-400 block">Fdo. Agua</span>
                                    <span class="font-semibold">{{ number_format($factEdif->sdo_act_fdo_agua ?? 0, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Totales --}}
                    <div class="bg-navy-800 text-white px-4 py-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between"><span>Facturacion del Mes</span><span class="font-bold">{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>Cobranza del Mes</span><span class="font-bold">{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RELACION DE GASTOS (1 col) --}}
            <div>
                <div class="card h-full print:shadow-none print:border print:border-black">
                    <div class="bg-burgundy-800 px-3 py-2">
                        <h4 class="text-xs font-bold text-white uppercase">Info General</h4>
                    </div>
                    <div class="p-3 text-xs space-y-3">
                        <div class="flex justify-between"><span class="text-slate_custom-500">Fecha Facturacion</span><span class="font-semibold">{{ $factEdif->fecha_fact?->format('d/m/Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-slate_custom-500">Fecha Calculo</span><span>{{ $factEdif->fecha_calculo?->format('d/m/Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-slate_custom-500">Plazo de Gracia</span><span>{{ $factEdif->plazo_gracia ?? 0 }} dias</span></div>
                        <div class="flex justify-between"><span class="text-slate_custom-500">% Fdo. Reserva</span><span>{{ number_format($factEdif->porc_fdo_res ?? 0, 2) }}%</span></div>
                        <div class="flex justify-between"><span class="text-slate_custom-500">Redondeo</span><span>{{ $factEdif->redondeo ?? 'N' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RELACION DE COBRO --}}
        @if($gastos->isNotEmpty())
        <div class="card mb-4 print:shadow-none print:border print:border-black">
            <div class="bg-burgundy-800 px-3 py-2">
                <h4 class="text-xs font-bold text-white uppercase">Relacion de Cobro - Gastos del Edificio</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate_custom-100">
                            <th class="px-2 py-1.5 text-left">CODIGO</th>
                            <th class="px-2 py-1.5 text-left">DESCRIPCION</th>
                            <th class="px-2 py-1.5 text-left">AMPLIACION</th>
                            <th class="px-2 py-1.5 text-right">MONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalGastos = 0; @endphp
                        @foreach($gastos as $gasto)
                        @php $totalGastos += ($gasto['monto'] ?? 0); @endphp
                        <tr class="border-b border-slate_custom-100 hover:bg-slate_custom-50">
                            <td class="px-2 py-1 font-mono">{{ $gasto['cod_gasto_legacy'] }}</td>
                            <td class="px-2 py-1">{{ $gasto['descripcion'] }}</td>
                            <td class="px-2 py-1 text-slate_custom-500">{{ $gasto['ampl_concepto'] }}</td>
                            <td class="px-2 py-1 text-right font-semibold">{{ number_format($gasto['monto'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-navy-800 text-white font-bold">
                            <td colspan="3" class="px-2 py-2 text-right">TOTAL EDIFICIO</td>
                            <td class="px-2 py-2 text-right">{{ number_format($totalGastos, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- FOOTER --}}
        <div class="card print:shadow-none print:border print:border-black">
            <div class="card-body p-3 flex items-center justify-between text-xs text-slate_custom-500">
                <span>Fecha Generacion: {{ $factEdif->fecha_fact?->format('Y-m-d') }}</span>
                <span>Fecha Impresion: {{ now()->format('d-m-Y') }}</span>
            </div>
        </div>
    </div>

    <style>
        @media print {
            nav, header, aside, .btn-primary, .btn-secondary, [x-data] { display: none !important; }
            body { background: white !important; }
            .card { break-inside: avoid; }
            #recibo-print { max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</x-app-layout>
