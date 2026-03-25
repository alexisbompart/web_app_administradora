<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Relacion Mensual del Condominio</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Periodo {{ $factApto->periodo }} - {{ $factApto->apartamento?->num_apto }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="btn-primary">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="{{ route('mi-condominio.recibos-apartamento') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $edificio = $factApto->edificio;
        $compania = $edificio?->compania;
        $apto = $factApto->apartamento;
        $periodo = $factApto->periodo;
        $fechaFact = $factApto->fecha_fact;
    @endphp

    <div class="max-w-5xl mx-auto print:max-w-full" id="recibo-print">
        {{-- HEADER --}}
        <div class="card mb-4 print:shadow-none print:border-2 print:border-black">
            <div class="card-body p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-navy-800">{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL E L B C A' }}</h3>
                        <p class="text-xs text-slate_custom-500">{{ $compania?->rif ?? '' }}</p>
                        <p class="text-xs text-slate_custom-500">{{ $compania?->direccion ?? '' }}</p>
                    </div>
                    <div class="text-center">
                        <h2 class="font-heading font-bold text-xl text-burgundy-800">RELACION MENSUAL<br>DEL CONDOMINIO</h2>
                        <p class="text-xs text-slate_custom-400 mt-1">Ref. tasa BCV al dia de emision {{ $fechaFact?->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right bg-slate_custom-50 rounded-lg p-3">
                        <p class="text-xs text-slate_custom-500">Codigo PINT</p>
                        <p class="text-sm font-bold text-navy-800">{{ $factApto->serial ?? '--' }}</p>
                        <p class="text-xs text-slate_custom-500 mt-1">Edif. {{ $edificio?->cod_edif }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATOS DEL INMUEBLE --}}
        <div class="card mb-4 print:shadow-none print:border print:border-black">
            <div class="card-body p-0">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-navy-800 text-white">
                            <th class="px-3 py-2 text-left">UNIDAD</th>
                            <th class="px-3 py-2 text-left">INMUEBLE</th>
                            <th class="px-3 py-2 text-left">PROPIETARIO</th>
                            <th class="px-3 py-2 text-center">ALICUOTA</th>
                            <th class="px-3 py-2 text-right">MONTO BS.</th>
                            <th class="px-3 py-2 text-right">REF</th>
                            <th class="px-3 py-2 text-center">MES/ANO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-3 py-2 font-semibold">{{ $apto?->num_apto }}</td>
                            <td class="px-3 py-2">{{ $edificio?->nombre }}</td>
                            <td class="px-3 py-2">{{ $factApto->nombre_propietario ?? $apto?->propietario_nombre }}</td>
                            <td class="px-3 py-2 text-center">{{ number_format($factApto->alicuota ?? $apto?->alicuota ?? 0, 6) }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($factApto->pago_total ?? 0, 2, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format(($factApto->pago_total ?? 0) / max($factApto->alicuota ?? 1, 0.001), 2, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center">{{ $periodo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            {{-- RELACION DE COBRO (left, 2 cols) --}}
            <div class="lg:col-span-2">
                <div class="card h-full print:shadow-none print:border print:border-black">
                    <div class="bg-burgundy-800 px-3 py-2">
                        <h4 class="text-xs font-bold text-white uppercase">Relacion de Cobro</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate_custom-100">
                                    <th class="px-2 py-1.5 text-left" colspan="2">CODIGO</th>
                                    <th class="px-2 py-1.5 text-left">CARGOS Y ABONOS COMUNES</th>
                                    <th class="px-2 py-1.5 text-left">AMPLIACION</th>
                                    <th class="px-2 py-1.5 text-right">ALICUOTA</th>
                                    <th class="px-2 py-1.5 text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalGastos = 0; @endphp
                                @forelse($gastos as $gasto)
                                @php
                                    $montoGasto = $gasto->monto ?? 0;
                                    $totalGastos += $montoGasto;
                                    $alicuotaGasto = $montoGasto * (($factApto->alicuota ?? 0) / 100);
                                @endphp
                                <tr class="border-b border-slate_custom-100 hover:bg-slate_custom-50">
                                    <td class="px-2 py-1 font-mono">{{ $gasto->cod_gasto_legacy }}</td>
                                    <td class="px-2 py-1 font-mono">{{ $gasto->cod_grupo ?? '0' }}</td>
                                    @php
                                        $desc = $gasto->concepto;
                                        if (!$desc || $desc == $gasto->cod_gasto_legacy) {
                                            $desc = $gastoCatalog[$gasto->cod_gasto_legacy . '|' . $gasto->tipo_gasto_legacy]
                                                 ?? $gastoCatalog[$gasto->cod_gasto_legacy]
                                                 ?? $gasto->ext_descripcion
                                                 ?? $gasto->cod_gasto_legacy;
                                        }
                                    @endphp
                                    <td class="px-2 py-1">{{ $desc }}</td>
                                    <td class="px-2 py-1 text-slate_custom-500">{{ $gasto->ampl_concepto ?? $gasto->ext_concepto ?? '' }}</td>
                                    <td class="px-2 py-1 text-right">{{ number_format($alicuotaGasto, 2, ',', '.') }}</td>
                                    <td class="px-2 py-1 text-right font-semibold">{{ number_format($montoGasto, 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-3 py-4 text-center text-slate_custom-400">Sin desglose disponible</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate_custom-100 font-bold">
                                    <td colspan="4" class="px-2 py-2 text-right">***TOTAL INMUEBLE</td>
                                    <td class="px-2 py-2 text-right"></td>
                                    <td class="px-2 py-2 text-right">{{ number_format($totalGastos, 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Mensaje --}}
                    <div class="bg-amber-50 border-t border-amber-200 px-3 py-2">
                        <p class="text-xs text-amber-800"><strong>MENSAJE:</strong> Su relacion del mes actual debe ser cancelada a partir del primer (01) dia del mes. Se procedera a calcular a la tasa de cambio oficial indicado por el BCV al momento del PAGO.</p>
                    </div>

                    {{-- Totales del recibo --}}
                    <div class="bg-navy-800 text-white px-3 py-2">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="flex justify-between"><span>MONTO Bs:</span><span class="font-bold">{{ number_format($factApto->pago_total ?? 0, 2, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>ABONO:</span><span>{{ number_format($factApto->otros_abonos ?? 0, 2, ',', '.') }}</span></div>
                            <div class="flex justify-between col-span-2 border-t border-white/20 pt-1"><span class="font-bold">SALDO POR PAGAR EN EL MES:</span><span class="font-bold text-lg">{{ number_format(($factApto->pago_total ?? 0) - ($factApto->otros_abonos ?? 0), 2, ',', '.') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-4">
                {{-- FONDO DE RESERVA / DEUDA COMUN --}}
                @if($factEdif)
                <div class="card print:shadow-none print:border print:border-black">
                    <div class="grid grid-cols-2 text-xs">
                        <div class="border-r border-slate_custom-200 p-3">
                            <h5 class="font-bold text-navy-800 mb-2 text-center bg-slate_custom-100 rounded py-1">FONDO DE RESERVA</h5>
                            <div class="space-y-1">
                                <div class="flex justify-between"><span class="text-slate_custom-500">SALDO ANTERIOR</span><span class="font-semibold">{{ number_format($factEdif->sdo_ant_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">MENOS CARGOS</span><span>{{ number_format($factEdif->cargos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">MAS ABONOS</span><span>{{ number_format($factEdif->abonos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t border-slate_custom-200 pt-1"><span class="font-bold">SALDO ACTUAL</span><span class="font-bold text-burgundy-800">{{ number_format($factEdif->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">INTERESES ACUM.</span><span>{{ number_format($factEdif->int_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div class="p-3">
                            <h5 class="font-bold text-navy-800 mb-2 text-center bg-slate_custom-100 rounded py-1">DEUDA COMUN</h5>
                            <div class="space-y-1">
                                <div class="flex justify-between"><span class="text-slate_custom-500">SALDO ANTERIOR</span><span class="font-semibold">{{ number_format($factEdif->deuda_ant_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">MENOS COBRANZA</span><span>{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">MAS FACTURACION</span><span>{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t border-slate_custom-200 pt-1"><span class="font-bold">SALDO ACTUAL</span><span class="font-bold text-red-600">{{ number_format($factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-slate_custom-500">No. RECIBOS PEND.</span><span class="font-bold">{{ $factEdif->recibos_pend ?? 0 }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ESTATUS DE PAGO --}}
                <div class="card print:shadow-none print:border print:border-black">
                    <div class="p-3 text-xs space-y-2">
                        <h5 class="font-bold text-navy-800 text-center bg-slate_custom-100 rounded py-1">ESTATUS DE PAGO</h5>
                        @if($pagoApto && $pagoApto->pago)
                        <div class="text-center">
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-700 font-bold rounded-full text-sm">PAGADO</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between"><span class="text-slate_custom-500">COMPROBANTE</span><span class="font-semibold">{{ $pagoApto->id_pago_legacy ?? $pagoApto->pago?->numero_recibo ?? $pagoApto->pago?->id }}</span></div>
                            <div class="flex justify-between"><span class="text-slate_custom-500">FECHA DE PAGO</span><span>{{ $pagoApto->fecha_pag?->format('d-M-Y') ?? $pagoApto->pago?->fecha_pago?->format('d-M-Y') ?? '--' }}</span></div>
                        </div>
                        @else
                        <div class="text-center">
                            <span class="inline-block px-3 py-1 bg-red-100 text-red-700 font-bold rounded-full text-sm">PENDIENTE</span>
                        </div>
                        @endif

                        <h5 class="font-bold text-navy-800 text-center bg-slate_custom-100 rounded py-1 mt-3">ESTATUS APARTAMENTO</h5>
                        <div class="text-center font-bold {{ ($factApto->mes_deuda ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ ($factApto->mes_deuda ?? 0) > 0 ? 'MOROSO' : 'MES PAGADO' }}
                        </div>
                    </div>
                </div>

                {{-- CARGOS Y ABONOS PARTICULARES --}}
                <div class="card print:shadow-none print:border print:border-black">
                    <div class="p-3 text-xs">
                        <h5 class="font-bold text-navy-800 text-center bg-slate_custom-100 rounded py-1 mb-2">CARGOS Y ABONOS PARTICULARES</h5>
                        <div class="grid grid-cols-3 gap-1 text-center">
                            <div><span class="text-slate_custom-400 block">TELEGRAMAS</span><span>{{ number_format($factApto->telegramas ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">CH. DVLTOS</span><span>{{ number_format($factApto->chq_dev ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">TOTAL CARGOS</span><span class="font-bold">{{ number_format(($factApto->telegramas ?? 0) + ($factApto->chq_dev ?? 0), 2, ',', '.') }}</span></div>
                        </div>
                        <div class="grid grid-cols-3 gap-1 text-center mt-2">
                            <div><span class="text-slate_custom-400 block">GESTIONES</span><span>{{ number_format($factApto->gestiones ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">IMPUESTOS</span><span>{{ number_format($factApto->impuestos ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">DSCTO. PRONTO</span><span>0.00</span></div>
                        </div>
                        <div class="grid grid-cols-3 gap-1 text-center mt-2">
                            <div><span class="text-slate_custom-400 block">HONORARIOS</span><span>{{ number_format($factApto->honorarios ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">OTROS ABONOS</span><span>{{ number_format($factApto->otros_abonos ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">CONVENIO</span><span>{{ number_format($factApto->convenios ?? 0, 2, ',', '.') }}</span></div>
                        </div>
                        <div class="grid grid-cols-2 gap-1 text-center mt-2 border-t border-slate_custom-200 pt-2">
                            <div><span class="text-slate_custom-400 block">TOTAL NO COMUN</span><span class="font-bold">{{ number_format($factApto->total_no_comun ?? 0, 2, ',', '.') }}</span></div>
                            <div><span class="text-slate_custom-400 block">INT. MORA</span><span class="font-bold text-red-600">{{ number_format($factApto->int_mora ?? 0, 2, ',', '.') }}</span></div>
                        </div>

                        {{-- TOTAL DEUDA / PAGO TOTAL --}}
                        <div class="bg-burgundy-800 text-white rounded mt-3 p-2">
                            <div class="flex justify-between items-center">
                                <div><span class="block text-white/70">TOTAL DEUDA</span><span class="font-bold">{{ $factApto->mes_deuda ?? 0 }} MESES</span></div>
                                <div class="text-right"><span class="block text-white/70">PAGO TOTAL</span><span class="font-bold text-lg">{{ number_format($factApto->pago_total ?? 0, 2, ',', '.') }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="card mt-4 print:shadow-none print:border print:border-black">
            <div class="card-body p-3 flex items-center justify-between text-xs text-slate_custom-500">
                <span>Fecha Generacion: {{ $fechaFact?->format('Y-m-d') }}</span>
                <span>Fecha Impresion: {{ now()->format('d-m-Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Print styles --}}
    <style>
        @media print {
            nav, header, aside, .btn-primary, .btn-secondary, [x-data] { display: none !important; }
            body { background: white !important; }
            .card { break-inside: avoid; }
            #recibo-print { max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</x-app-layout>
