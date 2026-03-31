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
            max-width: 1100px;
            margin: 60px auto 30px;
            padding: 20px;
        }

        .page {
            background: #fff;
            border: 1px solid #999;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }

        table { border-collapse: collapse; }

        /* OUTER TABLE: vertical labels + receipt + pint */
        .outer-table { width: 100%; border-collapse: collapse; }
        .outer-table > tbody > tr > td { vertical-align: top; }

        .vertical-left {
            width: 22px;
            background: #f0f0f0;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            position: relative;
        }
        .vertical-left .vtext {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 8px;
            font-weight: bold;
            color: #333;
            white-space: nowrap;
            letter-spacing: 0.5px;
        }
        .vertical-right {
            width: 22px;
            background: #f0f0f0;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        .vertical-right .vtext {
            writing-mode: vertical-rl;
            font-size: 9px;
            font-weight: bold;
            color: #333;
            white-space: nowrap;
        }

        .receipt-content { border: 1px solid #000; }

        /* ---- HEADER ---- */
        .header-row {
            display: flex;
            border-bottom: 2px solid #000;
        }
        .header-logo {
            flex: 0 0 180px;
            padding: 10px 12px;
            border-right: 1px solid #000;
            display: flex;
            align-items: center;
        }
        .header-logo .logo-text {
            font-size: 26px;
            font-weight: bold;
            color: #c2185b;
            line-height: 1;
        }
        .header-logo .logo-sub {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        .header-title {
            flex: 1;
            padding: 8px 12px;
            border-right: 1px solid #000;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-title h1 {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.2;
        }
        .header-pint {
            flex: 0 0 160px;
            padding: 8px 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-pint .val {
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 6px 14px;
        }

        .company-line {
            border-bottom: 1px solid #000;
            padding: 3px 8px;
            font-size: 10px;
            background: #fce4ec;
        }
        .company-line strong { margin-right: 6px; }

        /* ---- INMUEBLE TABLE ---- */
        .tbl-inmueble { width: 100%; border-bottom: 2px solid #000; }
        .tbl-inmueble th {
            background: #e8e8e8;
            color: #000;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #000;
            text-align: center;
        }
        .tbl-inmueble td {
            padding: 3px 6px;
            border: 1px solid #000;
            font-size: 10px;
            text-align: center;
            background: #fce4ec;
        }
        .tbl-inmueble td.b { font-weight: bold; }

        /* ---- MAIN BODY ---- */
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

        /* Cobro table */
        .tbl-cobro { width: 100%; flex: 1; }
        .tbl-cobro th {
            background: #e8e8e8;
            color: #000;
            padding: 3px 5px;
            font-size: 9px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            text-align: left;
        }
        .tbl-cobro th.r { text-align: right; }
        .tbl-cobro th.c { text-align: center; }
        .tbl-cobro td {
            padding: 1px 5px;
            border-bottom: 1px solid #eee;
            font-size: 9px;
            vertical-align: top;
        }
        .tbl-cobro td.r { text-align: right; font-family: 'Courier New', monospace; }
        .tbl-cobro td.c { text-align: center; }
        .tbl-cobro td.mono { font-family: 'Courier New', monospace; text-align: center; width: 28px; }

        .total-inmueble {
            border-top: 2px solid #000;
            padding: 4px 8px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        /* ---- RIGHT COLUMN ---- */
        .resumen-monto { border-bottom: 2px solid #000; }
        .resumen-monto table { width: 100%; }
        .resumen-monto td {
            padding: 2px 8px;
            font-size: 10px;
            border: 1px solid #000;
        }
        .resumen-monto td.r { text-align: right; font-weight: bold; }
        .resumen-monto tr.total td { font-weight: bold; font-size: 11px; }

        .section-title {
            background: #e8e8e8;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 5px;
            border-bottom: 1px solid #000;
        }

        .fondos-row { display: flex; border-bottom: 2px solid #000; }
        .fondo-col { flex: 1; font-size: 9px; }
        .fondo-col:first-child { border-right: 1px solid #000; }
        .fondo-col .data { padding: 2px 6px; }
        .fondo-col .data-row { display: flex; justify-content: space-between; padding: 1px 0; }
        .fondo-col .data-row .lbl { font-size: 8px; color: #333; font-weight: bold; }
        .fondo-col .data-row .val { font-weight: bold; font-size: 9px; }
        .fondo-col .data-row.sep { border-top: 1px solid #000; padding-top: 2px; margin-top: 2px; }

        .estatus-section { border-bottom: 1px solid #000; }
        .estatus-content { padding: 4px 8px; text-align: center; }
        .estatus-content .estado { font-size: 13px; font-weight: bold; }
        .estatus-content .pagado { color: #000; }
        .estatus-content .pendiente { color: #c00; }
        .estatus-detail { font-size: 9px; margin-top: 3px; }
        .estatus-detail .row { display: flex; justify-content: space-between; padding: 1px 0; }

        .particulares-section { border-bottom: 1px solid #000; }
        .tbl-particulares { width: 100%; border-collapse: collapse; }
        .tbl-particulares td {
            border: 1px solid #999;
            padding: 1px 3px;
            font-size: 8px;
            text-align: center;
        }
        .tbl-particulares td.lbl { font-size: 7px; color: #333; background: #f5f5f5; font-weight: bold; }

        .total-deuda-pago { border-bottom: 1px solid #000; }
        .tbl-totales { width: 100%; border-collapse: collapse; }
        .tbl-totales td {
            border: 1px solid #999;
            padding: 1px 4px;
            font-size: 8px;
            text-align: center;
        }
        .tbl-totales td.lbl { font-size: 7px; font-weight: bold; background: #f5f5f5; }
        .tbl-totales td.val { font-weight: bold; font-size: 9px; }

        .pint-label {
            background: #f0f0f0;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            color: #666;
            padding: 2px;
            border-top: 1px solid #000;
        }

        .footer-row {
            border-top: 2px solid #000;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f5f5f5;
            font-size: 9px;
        }
        .footer-row .btn-print {
            background: #ddd;
            border: 1px solid #999;
            padding: 2px 10px;
            font-size: 9px;
            font-weight: bold;
            cursor: pointer;
        }

        .grow { flex: 1; }

        /* ---- PRINT ---- */
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

    // Separar mes y año del periodo (YYYY-MM)
    $periodoMes = $periodo ? ltrim(substr($periodo, 5, 2), '0') : '--';
    $periodoAnio = $periodo ? substr($periodo, 0, 4) : '--';

    $pintCode = $factApto->serial ?? '--';
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

    {{-- OUTER TABLE with vertical texts --}}
    <table class="outer-table">
    <tbody>
    <tr>
        {{-- LEFT VERTICAL TEXT --}}
        <td class="vertical-left">
            <div class="vtext">Esta relaci&oacute;n NO es v&aacute;lida como comprobante de pago</div>
        </td>

        {{-- MAIN RECEIPT --}}
        <td class="receipt-content">

            {{-- ===== HEADER ===== --}}
            <div class="header-row">
                <div class="header-logo">
                    <div>
                        <div class="logo-text">integral</div>
                        <div class="logo-sub">Administradora</div>
                    </div>
                </div>
                <div class="header-title">
                    <h1>RELACI&Oacute;N MENSUAL<br>DEL CONDOMINIO</h1>
                </div>
                <div class="header-pint">
                    <div class="val">{{ $pintCode }}</div>
                </div>
            </div>

            {{-- COMPANY LINE --}}
            <div class="company-line">
                <strong>{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL E.L.B.,C.A.' }}</strong>
                {{ $compania?->rif ?? '' }}
                <span style="margin-left:8px;color:#555;font-size:9px;">{{ $compania?->direccion ?? '' }}</span>
            </div>

            {{-- ===== INMUEBLE TABLE ===== --}}
            <table class="tbl-inmueble">
                <thead>
                    <tr>
                        <th>UNIDAD</th>
                        <th>INMUEBLE</th>
                        <th>PROPIETARIO</th>
                        <th>ALICUOTA</th>
                        <th>MONTO_Bs.</th>
                        <th>MES</th>
                        <th>A&Ntilde;O</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="b">{{ $apto?->num_apto }}</td>
                        <td>{{ $edificio?->nombre }}</td>
                        <td>{{ $factApto->nombre_propietario ?? $apto?->propietario_nombre }}</td>
                        <td>{{ number_format($alicuota, 6) }}</td>
                        <td class="b">{{ number_format($montoBs, 2, ',', '.') }}</td>
                        <td class="b">{{ $periodoMes }}</td>
                        <td class="b">{{ $periodoAnio }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- ===== MAIN BODY ===== --}}
            <div class="body-row">

                {{-- LEFT: Relacion de cobro --}}
                <div class="body-left">
                    <table class="tbl-cobro">
                        <thead>
                            <tr>
                                <th colspan="2" class="c">C&Oacute;DIGO</th>
                                <th>CARGOS Y ABONOS COMUNES</th>
                                <th>AMPLIACI&Oacute;N DE CONCEPTOS</th>
                                <th class="r">ALICUOTA Bs.</th>
                                <th class="r">TOTAL Bs.</th>
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
                                <td style="color:#555;font-size:8px;">{{ $gasto->ampl_concepto ?? $gasto->ext_concepto ?? '' }}</td>
                                <td class="r">{{ number_format($alicuotaGasto, 2, ',', '.') }}</td>
                                <td class="r" style="font-weight:bold;">{{ number_format($montoGasto, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" style="text-align:center;padding:15px;color:#999;">Sin desglose disponible</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="grow"></div>

                    {{-- TOTAL INMUEBLE --}}
                    <div class="total-inmueble">
                        <span>***TOTAL INMUEBLE</span>
                        <span>{{ number_format($totalInmueble, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="body-right">

                    {{-- MONTO / ABONO / SALDO --}}
                    <div class="resumen-monto">
                        <table>
                            <tr>
                                <td><strong>MONTO:</strong></td>
                                <td class="r">{{ number_format($montoBs, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>ABONO:</strong></td>
                                <td class="r">{{ number_format($abono, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="total">
                                <td><strong>SALDO POR PAGAR EN EL MES</strong></td>
                                <td class="r" style="font-size:12px;">{{ number_format($saldoPagar, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- FONDO DE RESERVA / DEUDA COMUN --}}
                    @if($factEdif)
                    <div class="fondos-row">
                        <div class="fondo-col">
                            <div class="section-title">FONDO DE RESERVA Bs.</div>
                            <div class="data">
                                <div class="data-row"><span class="lbl">SALDO ANTERIOR</span></div>
                                <div class="data-row"><span></span><span class="val">{{ number_format((float)$factEdif->sdo_ant_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row"><span class="lbl">MENOS CARGOS</span></div>
                                <div class="data-row"><span></span><span>{{ number_format((float)$factEdif->cargos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row"><span class="lbl">M&Aacute;S ABONOS</span></div>
                                <div class="data-row"><span></span><span>{{ number_format((float)$factEdif->abonos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row sep"><span class="lbl">SALDO ACTUAL</span></div>
                                <div class="data-row"><span></span><span class="val">{{ number_format((float)$factEdif->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row" style="margin-top:3px;"><span class="lbl">INTERESES ACUM. Bs.</span></div>
                                <div class="data-row"><span></span><span>{{ number_format((float)$factEdif->int_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div class="fondo-col">
                            <div class="section-title">DEUDA COM&Uacute;N Bs.</div>
                            <div class="data">
                                <div class="data-row"><span class="lbl">SALDO ANTERIOR</span></div>
                                <div class="data-row"><span></span><span class="val">{{ number_format((float)$factEdif->deuda_ant_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row"><span class="lbl">MENOS COBRANZA</span></div>
                                <div class="data-row"><span></span><span>{{ number_format((float)$factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row"><span class="lbl">M&Aacute;S FACTURACI&Oacute;N DEL MES</span></div>
                                <div class="data-row"><span></span><span>{{ number_format((float)$factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row sep"><span class="lbl">SALDO ACTUAL</span></div>
                                <div class="data-row"><span></span><span class="val">{{ number_format((float)$factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</span></div>
                                <div class="data-row" style="margin-top:3px;"><span class="lbl">No. RECIBOS PENDIENTES</span></div>
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
                                    <div class="section-title" style="margin-top:4px;">NUMERO DE COMPROBANTE</div>
                                    <div style="font-weight:bold;font-size:12px;padding:3px 0;">{{ $pagoApto->id_pago_legacy ?? $pagoApto->pago?->numero_recibo ?? $pagoApto->pago?->id }}</div>
                                    <div class="section-title">FECHA DE PAGO</div>
                                    <div style="font-weight:bold;padding:3px 0;">{{ $pagoApto->fecha_pag?->format('d-M-Y') ?? $pagoApto->pago?->fecha_pago?->format('d-M-Y') ?? '--' }}</div>
                                </div>
                            @else
                                <div class="estado pendiente">PENDIENTE</div>
                            @endif
                        </div>
                    </div>

                    {{-- CARGOS Y ABONOS PARTICULARES --}}
                    <div class="particulares-section">
                        <div class="section-title">CARGOS Y ABONOS PARTICULARES</div>
                        <div style="padding:3px 4px;">
                            <table class="tbl-particulares">
                                <tr>
                                    <td class="lbl">TELEGRAMAS-CARTA</td>
                                    <td class="lbl">CH.DVLTOS. Y CARG.</td>
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
                                    <td>0,00</td>
                                </tr>
                                <tr>
                                    <td class="lbl">INDEM. DA&Ntilde;OS/PERJ.</td>
                                    <td class="lbl">HONORARIOS ADM.</td>
                                    <td class="lbl">OTROS ABONOS</td>
                                </tr>
                                <tr>
                                    <td>0,00</td>
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
                                    <td>0,00</td>
                                    <td style="font-weight:bold;">{{ number_format($factApto->total_no_comun ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="grow"></div>

                    {{-- ESTATUS APARTAMENTO / TOTALES --}}
                    <div class="total-deuda-pago">
                        <div class="section-title">ESTATUS APARTAMENTO</div>
                        <div style="padding:3px 4px;">
                            <table class="tbl-totales">
                                <tr>
                                    <td class="lbl">TOTAL DEUDA</td>
                                    <td class="lbl">PAGO TOTAL</td>
                                    <td class="lbl">PAGO PARCIAL</td>
                                </tr>
                                <tr>
                                    <td class="val">{{ $factApto->mes_deuda ?? 0 }} MESES</td>
                                    <td class="val">{{ number_format($montoBs, 2, ',', '.') }}</td>
                                    <td class="val">{{ number_format($factApto->pago_parcial ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- PINT label --}}
                    <div class="pint-label">PINT: {{ $pintCode }}</div>
                </div>
            </div>

            {{-- ===== FOOTER ===== --}}
            <div class="footer-row">
                <span>Fecha Generaci&oacute;n: <strong>{{ $fechaFact ? $fechaFact->format('Y-n-d') : '--' }}</strong></span>
                <button class="btn-print" onclick="window.print()">Imprimir</button>
                <span>Fecha Impresi&oacute;n: <strong>{{ now()->format('d-m-Y') }}</strong></span>
                <a href="{{ route('mi-condominio.recibos-apartamento') }}" class="btn-print" style="text-decoration:none;">Volver</a>
            </div>

        </td>

        {{-- RIGHT VERTICAL TEXT (PINT) --}}
        <td class="vertical-right">
            <div class="vtext">PINT:={{ $pintCode }}</div>
        </td>
    </tr>
    </tbody>
    </table>

</div>
</div>

</body>
</html>
