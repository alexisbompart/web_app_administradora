<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Edificio - {{ $factEdif->periodo }} - {{ $factEdif->edificio?->nombre }}</title>
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

        /* OUTER TABLE */
        .outer-table { width: 100%; border-collapse: collapse; }
        .outer-table > tbody > tr > td { vertical-align: top; }

        .vertical-left {
            width: 22px;
            background: #f0f0f0;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        .vertical-left .vtext {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 8px;
            font-weight: bold;
            color: #333;
            white-space: nowrap;
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

        /* HEADER */
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
        .header-logo .logo-text { font-size: 26px; font-weight: bold; color: #c2185b; line-height: 1; }
        .header-logo .logo-sub { font-size: 9px; color: #666; margin-top: 2px; }
        .header-title {
            flex: 1;
            padding: 8px 12px;
            border-right: 1px solid #000;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-title h1 { font-size: 16px; font-weight: bold; line-height: 1.2; }
        .header-title .sub { font-size: 11px; color: #c2185b; font-weight: bold; margin-top: 4px; }
        .header-edif {
            flex: 0 0 180px;
            padding: 8px 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-edif .lbl { font-size: 9px; color: #555; }
        .header-edif .val { font-size: 14px; font-weight: bold; border: 2px solid #000; padding: 4px 10px; display: inline-block; }

        .company-line {
            border-bottom: 1px solid #000;
            padding: 3px 8px;
            font-size: 10px;
            background: #fce4ec;
        }

        /* INMUEBLE TABLE */
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
            font-weight: bold;
        }

        /* MAIN BODY */
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
        .tbl-cobro td { padding: 1px 5px; border-bottom: 1px solid #eee; font-size: 9px; }
        .tbl-cobro td.r { text-align: right; font-family: 'Courier New', monospace; }
        .tbl-cobro td.c { text-align: center; font-family: 'Courier New', monospace; }

        .total-inmueble {
            border-top: 2px solid #000;
            padding: 4px 8px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        /* RIGHT COLUMN */
        .section-title {
            background: #e8e8e8;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 5px;
            border-bottom: 1px solid #000;
        }

        .resumen-monto { border-bottom: 2px solid #000; }
        .resumen-monto table { width: 100%; }
        .resumen-monto td { padding: 2px 8px; font-size: 10px; border: 1px solid #000; }
        .resumen-monto td.r { text-align: right; font-weight: bold; }

        .fondos-row { display: flex; border-bottom: 2px solid #000; }
        .fondo-col { flex: 1; font-size: 9px; }
        .fondo-col:first-child { border-right: 1px solid #000; }
        .fondo-col .data { padding: 2px 6px; }
        .fondo-col .data-row { display: flex; justify-content: space-between; padding: 1px 0; }
        .fondo-col .data-row .lbl { font-size: 8px; color: #333; font-weight: bold; }
        .fondo-col .data-row .val { font-weight: bold; font-size: 9px; }
        .fondo-col .data-row.sep { border-top: 1px solid #000; padding-top: 2px; margin-top: 2px; }

        .info-section { border-bottom: 1px solid #000; padding: 4px 8px; }
        .info-grid { display: flex; gap: 10px; flex-wrap: wrap; }
        .info-item { flex: 1; min-width: 100px; text-align: center; }
        .info-item .lbl { font-size: 8px; color: #555; font-weight: bold; }
        .info-item .val { font-size: 11px; font-weight: bold; }

        .otros-fondos { border-bottom: 1px solid #000; }
        .tbl-otros { width: 100%; border-collapse: collapse; }
        .tbl-otros td { border: 1px solid #999; padding: 1px 4px; font-size: 8px; text-align: center; }
        .tbl-otros td.lbl { font-size: 7px; font-weight: bold; background: #f5f5f5; }

        .resumen-bar { border-bottom: 1px solid #000; }
        .tbl-resumen { width: 100%; border-collapse: collapse; }
        .tbl-resumen td { border: 1px solid #999; padding: 2px 6px; font-size: 9px; text-align: center; }
        .tbl-resumen td.lbl { font-size: 7px; font-weight: bold; background: #f5f5f5; }
        .tbl-resumen td.val { font-weight: bold; font-size: 10px; }

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
    $edificio = $factEdif->edificio;
    $compania = $edificio?->compania;
    $periodo = $factEdif->periodo;
    $periodoMes = $periodo ? ltrim(substr($periodo, 5, 2), '0') : '--';
    $periodoAnio = $periodo ? substr($periodo, 0, 4) : '--';
@endphp

<div class="toolbar">
    <span class="title">Resumen Edificio - {{ $periodo }} - {{ $edificio?->nombre }}</span>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()">&#128424; Imprimir</button>
        <a href="{{ route('mi-condominio.recibos-edificio') }}">&#8592; Volver</a>
    </div>
</div>

<div class="page-wrapper">
<div class="page">

    <table class="outer-table">
    <tbody>
    <tr>
        <td class="vertical-left">
            <div class="vtext">Resumen financiero del edificio - Periodo {{ $periodo }}</div>
        </td>

        <td class="receipt-content">

            {{-- HEADER --}}
            <div class="header-row">
                <div class="header-logo">
                    <div>
                        <div class="logo-text">integral</div>
                        <div class="logo-sub">Administradora</div>
                    </div>
                </div>
                <div class="header-title">
                    <h1>RELACI&Oacute;N MENSUAL<br>DEL CONDOMINIO</h1>
                    <div class="sub">RESUMEN EDIFICIO</div>
                </div>
                <div class="header-edif">
                    <div class="lbl">Edificio</div>
                    <div class="val">{{ $edificio?->cod_edif }}</div>
                </div>
            </div>

            {{-- COMPANY LINE --}}
            <div class="company-line">
                <strong>{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL E.L.B.,C.A.' }}</strong>
                {{ $compania?->rif ?? '' }}
                <span style="margin-left:8px;color:#555;font-size:9px;">{{ $compania?->direccion ?? '' }}</span>
            </div>

            {{-- INMUEBLE TABLE --}}
            <table class="tbl-inmueble">
                <thead>
                    <tr>
                        <th>EDIFICIO</th>
                        <th>INMUEBLE</th>
                        <th>MONTO TOTAL Bs.</th>
                        <th>MES</th>
                        <th>A&Ntilde;O</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $edificio?->cod_edif }}</td>
                        <td>{{ $edificio?->nombre }}</td>
                        <td>{{ number_format((float)$factEdif->monto_total ?? 0, 2, ',', '.') }}</td>
                        <td>{{ $periodoMes }}</td>
                        <td>{{ $periodoAnio }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- MAIN BODY --}}
            <div class="body-row">

                {{-- LEFT: Relacion de cobro --}}
                <div class="body-left">
                    @if($gastos->isNotEmpty())
                    <table class="tbl-cobro">
                        <thead>
                            <tr>
                                <th style="width:70px;">C&Oacute;DIGO</th>
                                <th>DESCRIPCI&Oacute;N</th>
                                <th>AMPLIACI&Oacute;N</th>
                                <th class="r" style="width:110px;">MONTO Bs.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalGastos = 0; @endphp
                            @foreach($gastos as $gasto)
                            @php $totalGastos += ($gasto['monto'] ?? 0); @endphp
                            <tr>
                                <td class="c">{{ $gasto['cod_gasto_legacy'] }}</td>
                                <td style="text-transform:uppercase;">{{ $gasto['descripcion'] }}</td>
                                <td style="color:#555;font-size:8px;">{{ $gasto['ampl_concepto'] }}</td>
                                <td class="r" style="font-weight:bold;">{{ number_format($gasto['monto'], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div style="padding:30px;text-align:center;color:#999;flex:1;">
                        <p style="font-size:12px;font-weight:bold;">Sin desglose de gastos disponible</p>
                    </div>
                    @endif

                    <div class="grow"></div>

                    <div class="total-inmueble">
                        <span>***TOTAL EDIFICIO</span>
                        <span>{{ number_format($totalGastos ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="body-right">

                    {{-- MONTO TOTAL --}}
                    <div class="resumen-monto">
                        <table>
                            <tr>
                                <td><strong>FACTURACI&Oacute;N DEL MES:</strong></td>
                                <td class="r">{{ number_format((float)$factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>COBRANZA DEL MES:</strong></td>
                                <td class="r">{{ number_format((float)$factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>DEUDA ACTUAL:</strong></td>
                                <td class="r" style="font-size:12px;color:#c00;">{{ number_format((float)$factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- FONDOS --}}
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

                    {{-- INFO GENERAL --}}
                    <div class="info-section">
                        <div class="section-title">INFORMACI&Oacute;N GENERAL</div>
                        <div class="info-grid" style="padding:4px 0;">
                            <div class="info-item">
                                <div class="lbl">FECHA FACT.</div>
                                <div class="val">{{ $factEdif->fecha_fact?->format('d/m/Y') ?? '--' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">FECHA C&Aacute;LCULO</div>
                                <div class="val">{{ $factEdif->fecha_calculo?->format('d/m/Y') ?? '--' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">PLAZO GRACIA</div>
                                <div class="val">{{ $factEdif->plazo_gracia ?? 0 }} d&iacute;as</div>
                            </div>
                            <div class="info-item">
                                <div class="lbl">% FDO. RESERVA</div>
                                <div class="val">{{ number_format((float)$factEdif->porc_fdo_res ?? 0, 2) }}%</div>
                            </div>
                        </div>
                    </div>

                    {{-- OTROS FONDOS --}}
                    <div class="otros-fondos">
                        <div class="section-title">OTROS FONDOS</div>
                        <div style="padding:3px 4px;">
                            <table class="tbl-otros">
                                <tr>
                                    <td class="lbl">FDO. ESPECIAL</td>
                                    <td class="lbl">FDO. SOCIAL</td>
                                    <td class="lbl">FDO. AGUA</td>
                                </tr>
                                <tr>
                                    <td style="font-weight:bold;">{{ number_format((float)$factEdif->sdo_act_fdo_esp ?? 0, 2, ',', '.') }}</td>
                                    <td style="font-weight:bold;">{{ number_format((float)$factEdif->sdo_act_fdo_soc ?? 0, 2, ',', '.') }}</td>
                                    <td style="font-weight:bold;">{{ number_format((float)$factEdif->sdo_act_fdo_agua ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="grow"></div>

                    {{-- RESUMEN FINAL --}}
                    <div class="resumen-bar">
                        <div style="padding:3px 4px;">
                            <table class="tbl-resumen">
                                <tr>
                                    <td class="lbl">MONTO TOTAL</td>
                                    <td class="lbl">RECIBOS PEND.</td>
                                    <td class="lbl">REDONDEO</td>
                                </tr>
                                <tr>
                                    <td class="val">{{ number_format((float)$factEdif->monto_total ?? 0, 2, ',', '.') }}</td>
                                    <td class="val">{{ $factEdif->recibos_pend ?? 0 }}</td>
                                    <td class="val">{{ number_format((float)$factEdif->redondeo ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="footer-row">
                <span>Fecha Generaci&oacute;n: <strong>{{ $factEdif->fecha_fact?->format('Y-n-d') ?? '--' }}</strong></span>
                <button class="btn-print" onclick="window.print()">Imprimir</button>
                <span>Fecha Impresi&oacute;n: <strong>{{ now()->format('d-m-Y') }}</strong></span>
                <a href="{{ route('mi-condominio.recibos-edificio') }}" class="btn-print" style="text-decoration:none;">Volver</a>
            </div>

        </td>

        <td class="vertical-right">
            <div class="vtext">Edificio: {{ $edificio?->cod_edif }} - {{ $edificio?->nombre }}</div>
        </td>
    </tr>
    </tbody>
    </table>

</div>
</div>

</body>
</html>
