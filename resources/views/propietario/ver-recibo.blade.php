<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo Condominio - {{ $factApto->periodo }} - {{ $factApto->apartamento?->num_apto }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            background: #e5e7eb;
            color: #000;
        }

        .toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #273272;
            color: #fff;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        .toolbar a, .toolbar button {
            background: #fff;
            color: #273272;
            border: none;
            padding: 6px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .toolbar a:hover, .toolbar button:hover { background: #f0f0f0; }
        .toolbar .title { font-size: 14px; font-weight: bold; }

        .page-wrapper {
            max-width: 1050px;
            margin: 60px auto 30px;
            padding: 20px;
        }

        .page {
            background: #fff;
            border: 1px solid #999;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }

        /* ---- RECEIPT STRUCTURE ---- */
        table { border-collapse: collapse; }

        .header-row {
            display: flex;
            border-bottom: 2px solid #000;
        }
        .header-logo {
            flex: 0 0 200px;
            padding: 12px 15px;
            border-right: 1px solid #000;
            display: flex;
            align-items: center;
        }
        .header-logo .logo-text {
            font-size: 26px;
            font-weight: bold;
            color: #2e7d32;
            line-height: 1;
        }
        .header-logo .logo-sub {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }
        .header-title {
            flex: 1;
            padding: 10px 15px;
            border-right: 1px solid #000;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-title h1 {
            font-size: 17px;
            font-weight: bold;
            line-height: 1.2;
        }
        .header-title .ref-line {
            font-size: 9px;
            color: #555;
            margin-top: 6px;
        }
        .header-pint {
            flex: 0 0 150px;
            padding: 10px 12px;
            text-align: center;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-pint .lbl { font-size: 10px; color: #555; }
        .header-pint .val { font-size: 16px; font-weight: bold; }
        .header-pint .edif { font-size: 13px; font-weight: bold; margin-top: 4px; }

        .company-line {
            border-bottom: 1px solid #000;
            padding: 4px 10px;
            font-size: 11px;
        }
        .company-line strong { margin-right: 8px; }

        /* Inmueble table */
        .tbl-inmueble { width: 100%; border-bottom: 2px solid #000; }
        .tbl-inmueble th {
            background: #273272;
            color: #fff;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #000;
            text-align: left;
        }
        .tbl-inmueble th.r { text-align: right; }
        .tbl-inmueble th.c { text-align: center; }
        .tbl-inmueble td {
            padding: 5px 8px;
            border: 1px solid #000;
            font-size: 11px;
        }
        .tbl-inmueble td.r { text-align: right; }
        .tbl-inmueble td.c { text-align: center; }
        .tbl-inmueble td.b { font-weight: bold; }

        /* Main body */
        .body-row { display: flex; }
        .body-left {
            flex: 1;
            border-right: 2px solid #000;
            display: flex;
            flex-direction: column;
        }
        .body-right {
            flex: 0 0 310px;
            display: flex;
            flex-direction: column;
        }

        /* Relacion de cobro table */
        .tbl-cobro { width: 100%; }
        .tbl-cobro th {
            background: #273272;
            color: #fff;
            padding: 4px 6px;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            text-align: left;
        }
        .tbl-cobro th.r { text-align: right; }
        .tbl-cobro td {
            padding: 2px 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
            vertical-align: top;
        }
        .tbl-cobro td.r { text-align: right; font-family: 'Courier New', monospace; }
        .tbl-cobro td.c { text-align: center; }
        .tbl-cobro td.mono { font-family: 'Courier New', monospace; text-align: center; width: 30px; }
        .tbl-cobro tr:hover { background: #f9f9f9; }

        .total-inmueble {
            border-top: 2px solid #000;
            padding: 5px 8px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            background: #f0f0f0;
            font-size: 11px;
        }
        .total-edificio-ref {
            border-top: 1px solid #000;
            padding: 4px 8px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            background: #f0f0f0;
            font-size: 10px;
        }

        .mensaje-box {
            border-top: 2px solid #000;
            padding: 6px 8px;
            background: #fffde7;
            font-size: 9px;
            line-height: 1.4;
        }

        .resumen-monto {
            border-top: 2px solid #000;
            background: #273272;
            color: #fff;
        }
        .resumen-monto table { width: 100%; }
        .resumen-monto td { padding: 3px 8px; font-size: 11px; }
        .resumen-monto td.r { text-align: right; }
        .resumen-monto tr.total td { font-weight: bold; font-size: 13px; border-top: 1px solid rgba(255,255,255,.3); padding-top: 5px; padding-bottom: 5px; }

        /* Right column sections */
        .section-title {
            background: #ddd;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 4px 6px;
            border-bottom: 1px solid #000;
        }

        .fondos-row { display: flex; border-bottom: 2px solid #000; }
        .fondo-col {
            flex: 1;
            font-size: 10px;
        }
        .fondo-col:first-child { border-right: 1px solid #000; }
        .fondo-col .data { padding: 4px 8px; }
        .fondo-col .data-row { display: flex; justify-content: space-between; padding: 1px 0; }
        .fondo-col .data-row.sep { border-top: 1px solid #000; padding-top: 3px; margin-top: 2px; }
        .fondo-col .data-row .lbl { color: #333; }
        .fondo-col .data-row .val { font-weight: bold; }

        .estatus-section { border-bottom: 1px solid #000; }
        .estatus-content { padding: 6px 8px; text-align: center; }
        .estatus-content .estado { font-size: 14px; font-weight: bold; }
        .estatus-content .pagado { color: #000; }
        .estatus-content .pendiente { color: #c00; }
        .estatus-detail { font-size: 10px; margin-top: 4px; }
        .estatus-detail .row { display: flex; justify-content: space-between; padding: 1px 0; }

        .particulares-section { border-bottom: 1px solid #000; }
        .tbl-particulares { width: 100%; border-collapse: collapse; margin: 4px 0; }
        .tbl-particulares td {
            border: 1px solid #ccc;
            padding: 2px 4px;
            font-size: 9px;
            text-align: center;
        }
        .tbl-particulares td.lbl { font-size: 8px; color: #666; background: #fafafa; }

        .total-deuda-pago {
            background: #680c3e;
            color: #fff;
            padding: 6px 10px;
        }
        .total-deuda-pago .row { display: flex; justify-content: space-between; align-items: baseline; }
        .total-deuda-pago .lbl { font-size: 9px; color: rgba(255,255,255,.7); }
        .total-deuda-pago .val { font-size: 14px; font-weight: bold; }

        .pint-label {
            background: #f0f0f0;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #666;
            padding: 3px;
            border-top: 1px solid #000;
        }

        /* Footer */
        .footer-row {
            border-top: 2px solid #000;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f5f5f5;
            font-size: 10px;
        }
        .footer-row a {
            color: #1565c0;
            text-decoration: underline;
            cursor: pointer;
        }
        .footer-row .btn-print {
            background: #ddd;
            border: 1px solid #999;
            padding: 3px 12px;
            font-size: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .footer-row .btn-print:hover { background: #ccc; }

        .grow { flex: 1; }

        /* ---- PRINT STYLES ---- */
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .page { border: none; box-shadow: none; }
            .footer-row .btn-print { display: none; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            @page { size: A4 landscape; margin: 5mm; }
        }
    </style>
</head>
<body>

@php
    $edificio = $factApto->edificio;
    $compania = $edificio?->compania;
    $apto = $factApto->apartamento;
    $periodo = $factApto->periodo;
    $fechaFact = $factApto->fecha_fact;
    $montoBs = $factApto->pago_total ?? 0;
    $abono = $factApto->otros_abonos ?? 0;
    $saldoPagar = $montoBs - $abono;
    $alicuota = $factApto->alicuota ?? $apto?->alicuota ?? 0;
    $ref = $alicuota > 0 ? $montoBs / ($alicuota / 100) : 0;
    $periodoDisplay = $periodo ? \Carbon\Carbon::createFromFormat('Y-m', $periodo)?->format('n/Y') : '--';
@endphp

{{-- TOOLBAR --}}
<div class="toolbar">
    <span class="title">Relacion Mensual del Condominio - {{ $periodo }} - {{ $apto?->num_apto }}</span>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()">&#128424; Imprimir</button>
        <a href="{{ route('mi-condominio.recibos-apartamento') }}">&#8592; Volver</a>
    </div>
</div>

<div class="page-wrapper">
<div class="page">

    {{-- ============================================ --}}
    {{-- HEADER                                       --}}
    {{-- ============================================ --}}
    <div class="header-row">
        <div class="header-logo">
            <div>
                <div class="logo-text">integral</div>
                <div class="logo-sub">Administradora</div>
            </div>
        </div>
        <div class="header-title">
            <h1>RELACI&Oacute;N MENSUAL<br>DEL CONDOMINIO</h1>
            <div class="ref-line">
                Ref. del calculo tasa del BCV al dia de emision de su residencia el
                {{ $fechaFact ? $fechaFact->format('d/ n / Y') : '--' }}
                por Bs.{{ number_format($ref > 0 ? $ref / 100 : 0, 2, '.', ',') }}
            </div>
        </div>
        <div class="header-pint">
            <div class="lbl">Codigo PINT</div>
            <div class="val">{{ $factApto->serial ?? '--' }}</div>
            <div class="edif">Edif. {{ $edificio?->cod_edif }}</div>
        </div>
    </div>

    {{-- COMPANY LINE --}}
    <div class="company-line">
        <strong>{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL' }}</strong>
        {{ $compania?->rif ?? '' }}
        <span style="margin-left:10px;color:#555;">{{ $compania?->direccion ?? '' }}</span>
    </div>

    {{-- ============================================ --}}
    {{-- DATOS DEL INMUEBLE                           --}}
    {{-- ============================================ --}}
    <table class="tbl-inmueble">
        <thead>
            <tr>
                <th>UNIDAD</th>
                <th>INMUEBLE</th>
                <th>PROPIETARIO</th>
                <th class="c">ALICUOTA</th>
                <th class="r">MONTO BS.</th>
                <th class="r">REF</th>
                <th class="c">MES/A&Ntilde;O</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="b">{{ $apto?->num_apto }}</td>
                <td>{{ $edificio?->nombre }}</td>
                <td>{{ $factApto->nombre_propietario ?? $apto?->propietario_nombre }}</td>
                <td class="c">{{ number_format($alicuota, 6) }}</td>
                <td class="r b">{{ number_format($montoBs, 2, ',', '.') }}</td>
                <td class="r">{{ number_format($ref, 2, ',', '.') }}</td>
                <td class="c b">{{ $periodoDisplay }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ============================================ --}}
    {{-- MAIN BODY: LEFT (cobro) + RIGHT (status)     --}}
    {{-- ============================================ --}}
    <div class="body-row">

        {{-- LEFT COLUMN: Relacion de Cobro --}}
        <div class="body-left">
            <table class="tbl-cobro">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align:center;">C&Oacute;DIGO</th>
                        <th>CARGOS Y ABONOS COMUNES</th>
                        <th>AMPLIACI&Oacute;N DE CONCEPTOS</th>
                        <th class="r">ALICUOTA</th>
                        <th class="r">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalInmueble = 0; @endphp
                    @forelse($gastos as $gasto)
                    @php
                        $montoGasto = $gasto->monto ?? 0;
                        $totalInmueble += $montoGasto;
                        $alicuotaGasto = $montoGasto * ($alicuota / 100);

                        $desc = $gasto->concepto;
                        if (!$desc || $desc == $gasto->cod_gasto_legacy) {
                            $desc = $gastoCatalog[$gasto->cod_gasto_legacy . '|' . $gasto->tipo_gasto_legacy]
                                 ?? $gastoCatalog[$gasto->cod_gasto_legacy]
                                 ?? $gasto->ext_descripcion
                                 ?? $gasto->cod_gasto_legacy;
                        }
                    @endphp
                    <tr>
                        <td class="mono">{{ $gasto->cod_gasto_legacy }}</td>
                        <td class="mono">{{ $gasto->cod_grupo ?? '0' }}</td>
                        <td style="text-transform:uppercase;">{{ $desc }}</td>
                        <td style="color:#666;">{{ $gasto->ampl_concepto ?? $gasto->ext_concepto ?? '' }}</td>
                        <td class="r">{{ number_format($alicuotaGasto, 2, ',', '.') }}</td>
                        <td class="r" style="font-weight:bold;">{{ number_format($montoGasto, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:20px;color:#999;">Sin desglose disponible</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="grow"></div>

            {{-- TOTAL INMUEBLE --}}
            <div class="total-inmueble">
                <span>***TOTAL INMUEBLE</span>
                <span>{{ number_format($totalInmueble, 2, ',', '.') }}</span>
            </div>

            {{-- TOTAL EDIFICIO REF BCV --}}
            <div class="total-edificio-ref">
                <span>***TOTAL EDIFICIO REF. BCV</span>
                <span>{{ number_format($alicuota > 0 ? $totalInmueble * ($alicuota / 100) : 0, 2, ',', '.') }}</span>
            </div>

            {{-- MENSAJE --}}
            <div class="mensaje-box">
                <strong>MENSAJE:</strong> Su relacion del mes actual debe ser cancelada a partir del primer (01)
                d&iacute;a del mes se procedera a calcular a la tasa de cambio oficial indicado por el BCV
                al momento del PAGO, de conformidad con los art&iacute;culos 1&deg; y 8&deg; del Convenio
                Cambiario N&deg; 6405 de Fecha 07/09/2018 publicado en Gaceta oficial de la Rep&uacute;blica
                Bolivariana de Venezuela
            </div>

            {{-- RESUMEN: MONTO / ABONO / SALDO --}}
            <div class="resumen-monto">
                <table>
                    <tr>
                        <td>MONTO Bs:</td>
                        <td class="r" style="font-weight:bold;">{{ number_format($montoBs, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>ABONO:</td>
                        <td class="r">{{ number_format($abono, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="total">
                        <td>SALDO POR PAGAR EN EL MES:</td>
                        <td class="r">{{ number_format($saldoPagar, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="body-right">

            {{-- FONDO DE RESERVA / DEUDA COMUN --}}
            @if($factEdif)
            <div class="fondos-row">
                <div class="fondo-col">
                    <div class="section-title">FONDO DE RESERVA</div>
                    <div class="data">
                        <div class="data-row"><span class="lbl">SALDO ANTERIOR</span></div>
                        <div class="data-row"><span></span><span class="val">{{ number_format($factEdif->sdo_ant_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row"><span class="lbl">MENOS CARGOS</span></div>
                        <div class="data-row"><span></span><span>{{ number_format($factEdif->cargos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row"><span class="lbl">M&Aacute;S ABONOS</span></div>
                        <div class="data-row"><span></span><span>{{ number_format($factEdif->abonos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row sep"><span class="lbl" style="font-weight:bold;">SALDO ACTUAL</span></div>
                        <div class="data-row"><span></span><span class="val">{{ number_format($factEdif->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row" style="margin-top:4px;"><span class="lbl">INTERESES ACUM.</span></div>
                        <div class="data-row"><span></span><span>{{ number_format($factEdif->int_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                    </div>
                </div>
                <div class="fondo-col">
                    <div class="section-title">DEUDA COM&Uacute;N</div>
                    <div class="data">
                        <div class="data-row"><span class="lbl">SALDO ANTERIOR</span></div>
                        <div class="data-row"><span></span><span class="val">{{ number_format($factEdif->deuda_ant_edif ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row"><span class="lbl">MENOS COBRANZA</span></div>
                        <div class="data-row"><span></span><span>{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row"><span class="lbl">M&Aacute;S FACTURACI&Oacute;N DEL MES</span></div>
                        <div class="data-row"><span></span><span>{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row sep"><span class="lbl" style="font-weight:bold;">SALDO ACTUAL</span></div>
                        <div class="data-row"><span></span><span class="val">{{ number_format($factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</span></div>
                        <div class="data-row" style="margin-top:4px;"><span class="lbl">No. RECIBOS PENDIENTES</span></div>
                        <div class="data-row"><span></span><span class="val">{{ $factEdif->recibos_pend ?? 0 }}</span></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ESTATUS DE PAGO --}}
            <div class="estatus-section">
                <div class="section-title">ESTATUS DE PAGO</div>
                <div class="estatus-content">
                    @if($pagoApto && $pagoApto->pago)
                        <div class="estado pagado">PAGADO</div>
                        <div class="estatus-detail">
                            <div class="row"><span>NUMERO DE COMPROBANTE</span></div>
                            <div style="font-weight:bold;font-size:12px;">{{ $pagoApto->id_pago_legacy ?? $pagoApto->pago?->numero_recibo ?? $pagoApto->pago?->id }}</div>
                            <div class="row" style="margin-top:4px;"><span>FECHA DE PAGO</span></div>
                            <div style="font-weight:bold;">{{ $pagoApto->fecha_pag?->format('d-M-Y') ?? $pagoApto->pago?->fecha_pago?->format('d-M-Y') ?? '--' }}</div>
                        </div>
                    @else
                        <div class="estado pendiente">PENDIENTE</div>
                    @endif
                </div>
            </div>

            {{-- ESTATUS APARTAMENTO --}}
            <div class="estatus-section">
                <div class="section-title">ESTATUS APARTAMENTO</div>
                <div class="estatus-content">
                    <div class="estado" style="color:{{ ($factApto->mes_deuda ?? 0) > 0 ? '#c00' : '#2e7d32' }};">
                        {{ ($factApto->mes_deuda ?? 0) > 0 ? 'MOROSO' : 'MES PAGADO' }}
                    </div>
                </div>
            </div>

            {{-- CARGOS Y ABONOS PARTICULARES --}}
            <div class="particulares-section">
                <div class="section-title">CARGOS Y ABONOS PARTICULARES</div>
                <div style="padding:4px 6px;">
                    <table class="tbl-particulares">
                        <tr>
                            <td class="lbl">TELEGRAMAS-CARTACH.</td>
                            <td class="lbl">DVLTOS. Y CARG.</td>
                            <td class="lbl">TOTAL CARGOS</td>
                        </tr>
                        <tr>
                            <td>{{ number_format($factApto->telegramas ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($factApto->chq_dev ?? 0, 2, ',', '.') }}</td>
                            <td style="font-weight:bold;">{{ number_format(($factApto->telegramas ?? 0) + ($factApto->chq_dev ?? 0), 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">GESTIONES</td>
                            <td class="lbl">IMPUESTOS</td>
                            <td class="lbl">DSCTO. PRONTO PAGO</td>
                        </tr>
                        <tr>
                            <td>{{ number_format($factApto->gestiones ?? 0, 2, ',', '.') }}</td>
                            <td>IVA-{{ number_format($factApto->impuestos ?? 0, 2, ',', '.') }}</td>
                            <td>0.00</td>
                        </tr>
                        <tr>
                            <td class="lbl">INDEM. DA&Ntilde;OS/PERJ.</td>
                            <td class="lbl">HONORARIOS ADM.</td>
                            <td class="lbl">OTROS ABONOS</td>
                        </tr>
                        <tr>
                            <td>0.00</td>
                            <td>{{ number_format($factApto->honorarios ?? 0, 2, ',', '.') }}</td>
                            <td>{{ number_format($factApto->otros_abonos ?? 0, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">CONVENIO DE PAG.</td>
                            <td class="lbl">OTROS CARGOS</td>
                            <td class="lbl">TOTAL NO COM&Uacute;N</td>
                        </tr>
                        <tr>
                            <td>{{ number_format($factApto->convenios ?? 0, 2, ',', '.') }}</td>
                            <td>0.00</td>
                            <td style="font-weight:bold;">{{ number_format($factApto->total_no_comun ?? 0, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="grow"></div>

            {{-- TOTAL DEUDA / PAGO TOTAL --}}
            <div class="total-deuda-pago">
                <div class="row">
                    <div>
                        <div class="lbl">TOTAL DEUDA</div>
                        <div class="val">{{ $factApto->mes_deuda ?? 0 }} MESES</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="lbl">PAGO TOTAL</div>
                        <div class="val">{{ number_format($montoBs, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- PINT label --}}
            <div class="pint-label">PINT: {{ $factApto->serial ?? '--' }}</div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FOOTER                                       --}}
    {{-- ============================================ --}}
    <div class="footer-row">
        <span><a href="#">Condiciones Generales</a></span>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
        <span>Fecha Generaci&oacute;n de recibo: &nbsp;<strong>{{ $fechaFact ? $fechaFact->format('Y-n-d') : '--' }}</strong></span>
        <span>Fecha Impresi&oacute;n: &nbsp;<strong>{{ now()->format('d-m-Y') }}</strong></span>
        <a href="{{ route('mi-condominio.recibos-apartamento') }}" class="btn-print">Volver</a>
    </div>

</div>
</div>

</body>
</html>
