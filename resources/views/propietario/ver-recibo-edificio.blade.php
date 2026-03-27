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
            max-width: 1050px;
            margin: 60px auto 30px;
            padding: 20px;
        }

        .page {
            background: #fff;
            border: 1px solid #999;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }

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
        .header-logo .logo-text { font-size: 26px; font-weight: bold; color: #2e7d32; line-height: 1; }
        .header-logo .logo-sub { font-size: 10px; color: #666; margin-top: 2px; }
        .header-title {
            flex: 1;
            padding: 10px 15px;
            border-right: 1px solid #000;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-title h1 { font-size: 17px; font-weight: bold; line-height: 1.2; }
        .header-title .sub { font-size: 12px; color: #680c3e; font-weight: bold; margin-top: 4px; }
        .header-edif {
            flex: 0 0 180px;
            padding: 10px 12px;
            text-align: center;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .header-edif .lbl { font-size: 10px; color: #555; }
        .header-edif .val { font-size: 14px; font-weight: bold; }

        .company-line {
            border-bottom: 1px solid #000;
            padding: 4px 10px;
            font-size: 11px;
        }

        /* Fondos section */
        .fondos-grid { display: flex; border-bottom: 2px solid #000; }
        .fondo-box { flex: 1; border-right: 1px solid #000; }
        .fondo-box:last-child { border-right: none; }
        .fondo-box .title-bar { background: #273272; color: #fff; text-align: center; font-weight: bold; font-size: 10px; padding: 5px 8px; }
        .fondo-box .content { padding: 8px 10px; }
        .fondo-box .row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 11px; }
        .fondo-box .row.sep { border-top: 1px solid #000; margin-top: 4px; padding-top: 4px; }
        .fondo-box .row .lbl { color: #333; }
        .fondo-box .row .val { font-weight: bold; }

        /* Info section */
        .info-grid { display: flex; border-bottom: 2px solid #000; }
        .info-box { flex: 1; border-right: 1px solid #000; padding: 8px 12px; }
        .info-box:last-child { border-right: none; }
        .info-box .label { font-size: 9px; color: #666; text-transform: uppercase; }
        .info-box .value { font-size: 13px; font-weight: bold; margin-top: 2px; }

        /* Cobro table */
        .tbl-cobro { width: 100%; }
        .tbl-cobro th {
            background: #273272; color: #fff;
            padding: 5px 8px; font-size: 10px; font-weight: bold;
            border-bottom: 1px solid #000; text-align: left;
        }
        .tbl-cobro th.r { text-align: right; }
        .tbl-cobro td { padding: 3px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        .tbl-cobro td.r { text-align: right; font-family: 'Courier New', monospace; }
        .tbl-cobro td.c { text-align: center; font-family: 'Courier New', monospace; }
        .tbl-cobro tr:hover { background: #f9f9f9; }

        .total-bar {
            background: #680c3e; color: #fff;
            padding: 8px 12px;
            display: flex; justify-content: space-between;
            font-weight: bold; font-size: 13px;
        }

        .resumen-bar {
            background: #273272; color: #fff;
            display: flex;
        }
        .resumen-bar .cell {
            flex: 1; padding: 8px 12px;
            border-right: 1px solid rgba(255,255,255,.2);
            text-align: center;
        }
        .resumen-bar .cell:last-child { border-right: none; }
        .resumen-bar .cell .lbl { font-size: 9px; color: rgba(255,255,255,.7); }
        .resumen-bar .cell .val { font-size: 14px; font-weight: bold; margin-top: 2px; }

        .footer-row {
            border-top: 2px solid #000;
            padding: 5px 10px;
            display: flex; align-items: center; justify-content: space-between;
            background: #f5f5f5; font-size: 10px;
        }
        .footer-row .btn-print {
            background: #ddd; border: 1px solid #999;
            padding: 3px 12px; font-size: 10px; font-weight: bold; cursor: pointer;
        }

        .grow { flex: 1; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page-wrapper { margin: 0; padding: 0; max-width: 100%; }
            .page { border: none; box-shadow: none; }
            .footer-row .btn-print { display: none; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            @page { size: A4 portrait; margin: 8mm; }
        }
    </style>
</head>
<body>

@php
    $edificio = $factEdif->edificio;
    $compania = $edificio?->compania;
@endphp

<div class="toolbar">
    <span class="title">Resumen Edificio - {{ $factEdif->periodo }} - {{ $edificio?->nombre }}</span>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()">&#128424; Imprimir</button>
        <a href="{{ route('mi-condominio.recibos-edificio') }}">&#8592; Volver</a>
    </div>
</div>

<div class="page-wrapper">
<div class="page">

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
            <div class="val">{{ $edificio?->nombre }}</div>
            <div style="font-size:10px;color:#555;margin-top:4px;">Cod. {{ $edificio?->cod_edif }}</div>
            <div style="font-size:10px;font-weight:bold;margin-top:2px;">Periodo: {{ $factEdif->periodo }}</div>
        </div>
    </div>

    {{-- COMPANY LINE --}}
    <div class="company-line">
        <strong>{{ $compania?->nombre ?? 'ADMINISTRADORA INTEGRAL' }}</strong>
        {{ $compania?->rif ?? '' }}
        <span style="margin-left:10px;color:#555;">{{ $compania?->direccion ?? '' }}</span>
    </div>

    {{-- FONDOS Y DEUDA --}}
    <div class="fondos-grid">
        <div class="fondo-box">
            <div class="title-bar">FONDO DE RESERVA</div>
            <div class="content">
                <div class="row"><span class="lbl">SALDO ANTERIOR</span><span class="val">{{ number_format($factEdif->sdo_ant_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">MENOS CARGOS</span><span>{{ number_format($factEdif->cargos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">M&Aacute;S ABONOS</span><span>{{ number_format($factEdif->abonos_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                <div class="row sep"><span class="lbl" style="font-weight:bold;">SALDO ACTUAL</span><span class="val" style="color:#680c3e;">{{ number_format($factEdif->sdo_act_fdo_res ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">INTERESES ACUM.</span><span>{{ number_format($factEdif->int_fdo_res ?? 0, 2, ',', '.') }}</span></div>
            </div>
        </div>
        <div class="fondo-box">
            <div class="title-bar">DEUDA COM&Uacute;N</div>
            <div class="content">
                <div class="row"><span class="lbl">SALDO ANTERIOR</span><span class="val">{{ number_format($factEdif->deuda_ant_edif ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">MENOS COBRANZA</span><span>{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">M&Aacute;S FACTURACI&Oacute;N</span><span>{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</span></div>
                <div class="row sep"><span class="lbl" style="font-weight:bold;">SALDO ACTUAL</span><span class="val" style="color:#c00;">{{ number_format($factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">RECIBOS PEND.</span><span class="val">{{ $factEdif->recibos_pend ?? 0 }}</span></div>
            </div>
        </div>
        <div class="fondo-box">
            <div class="title-bar">OTROS FONDOS</div>
            <div class="content">
                <div class="row"><span class="lbl">FDO. ESPECIAL</span><span class="val">{{ number_format($factEdif->sdo_act_fdo_esp ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">FDO. SOCIAL</span><span class="val">{{ number_format($factEdif->sdo_act_fdo_soc ?? 0, 2, ',', '.') }}</span></div>
                <div class="row"><span class="lbl">FDO. AGUA</span><span class="val">{{ number_format($factEdif->sdo_act_fdo_agua ?? 0, 2, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    {{-- INFO GENERAL --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="label">Fecha Facturacion</div>
            <div class="value">{{ $factEdif->fecha_fact?->format('d/m/Y') ?? '--' }}</div>
        </div>
        <div class="info-box">
            <div class="label">Fecha Calculo</div>
            <div class="value">{{ $factEdif->fecha_calculo?->format('d/m/Y') ?? '--' }}</div>
        </div>
        <div class="info-box">
            <div class="label">Plazo de Gracia</div>
            <div class="value">{{ $factEdif->plazo_gracia ?? 0 }} dias</div>
        </div>
        <div class="info-box">
            <div class="label">% Fdo. Reserva</div>
            <div class="value">{{ number_format($factEdif->porc_fdo_res ?? 0, 2) }}%</div>
        </div>
    </div>

    {{-- RELACION DE COBRO --}}
    @if($gastos->isNotEmpty())
    <table class="tbl-cobro">
        <thead>
            <tr>
                <th style="width:80px;">C&Oacute;DIGO</th>
                <th>DESCRIPCI&Oacute;N</th>
                <th>AMPLIACI&Oacute;N</th>
                <th class="r" style="width:120px;">MONTO</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGastos = 0; @endphp
            @foreach($gastos as $gasto)
            @php $totalGastos += ($gasto['monto'] ?? 0); @endphp
            <tr>
                <td class="c">{{ $gasto['cod_gasto_legacy'] }}</td>
                <td style="text-transform:uppercase;">{{ $gasto['descripcion'] }}</td>
                <td style="color:#666;">{{ $gasto['ampl_concepto'] }}</td>
                <td class="r" style="font-weight:bold;">{{ number_format($gasto['monto'], 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-bar">
        <span>***TOTAL EDIFICIO</span>
        <span>{{ number_format($totalGastos, 2, ',', '.') }}</span>
    </div>
    @else
    <div style="padding:40px;text-align:center;color:#999;">
        <div style="font-size:40px;margin-bottom:10px;">&#128196;</div>
        <p style="font-size:14px;font-weight:bold;">Sin desglose de gastos disponible para este periodo</p>
        <p style="font-size:11px;margin-top:5px;">Los datos de pre-facturacion no han sido importados para el edificio {{ $edificio?->nombre }} periodo {{ $factEdif->periodo }}.</p>
    </div>
    @endif

    {{-- RESUMEN BAR --}}
    <div class="resumen-bar">
        <div class="cell">
            <div class="lbl">FACTURACI&Oacute;N DEL MES</div>
            <div class="val">{{ number_format($factEdif->facturacion_edif ?? 0, 2, ',', '.') }}</div>
        </div>
        <div class="cell">
            <div class="lbl">COBRANZA DEL MES</div>
            <div class="val">{{ number_format($factEdif->cobranza_edif ?? 0, 2, ',', '.') }}</div>
        </div>
        <div class="cell">
            <div class="lbl">DEUDA ACTUAL</div>
            <div class="val" style="color:#ff6b6b;">{{ number_format($factEdif->deuda_act_edif ?? 0, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer-row">
        <span>Fecha Generaci&oacute;n: <strong>{{ $factEdif->fecha_fact?->format('Y-n-d') ?? '--' }}</strong></span>
        <button class="btn-print" onclick="window.print()">Imprimir</button>
        <span>Fecha Impresi&oacute;n: <strong>{{ now()->format('d-m-Y') }}</strong></span>
        <a href="{{ route('mi-condominio.recibos-edificio') }}" class="btn-print" style="text-decoration:none;">Volver</a>
    </div>

</div>
</div>

</body>
</html>
