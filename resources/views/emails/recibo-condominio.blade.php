<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Condominio</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f5f7; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f5f7; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e293b, #7f1d1d); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 22px; margin: 0;">{{ $companiaNombre }}</h1>
                            <p style="color: #cbd5e1; font-size: 13px; margin: 5px 0 0;">Recibo de Condominio</p>
                        </td>
                    </tr>

                    <!-- Saludo -->
                    <tr>
                        <td style="padding: 30px 30px 15px;">
                            <p style="font-size: 15px; color: #334155; margin: 0;">
                                Estimado(a) <strong>{{ $propietarioNombre }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #64748b; margin: 10px 0 0;">
                                Le informamos que se ha generado su recibo de condominio correspondiente al periodo <strong>{{ $deuda->periodo }}</strong> del edificio <strong>{{ $edificioNombre }}</strong>.
                            </p>
                        </td>
                    </tr>

                    <!-- Detalles del Recibo -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #1e293b; font-size: 15px; margin: 0 0 15px; border-bottom: 2px solid #7f1d1d; padding-bottom: 8px;">
                                            Detalle del Recibo
                                        </h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Edificio:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $edificioNombre }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Apartamento:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $deuda->apartamento->num_apto }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Periodo:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $deuda->periodo }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Fecha Emision:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $deuda->fecha_emision ? \Carbon\Carbon::parse($deuda->fecha_emision)->format('d/m/Y') : '--' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Fecha Vencimiento:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $deuda->fecha_vencimiento ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)->format('d/m/Y') : '--' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Desglose de Montos -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #1e293b; font-size: 15px; margin: 0 0 15px; border-bottom: 2px solid #7f1d1d; padding-bottom: 8px;">
                                            Desglose
                                        </h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Monto Original</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ number_format($deuda->monto_original, 2, ',', '.') }} Bs</td>
                                            </tr>
                                            @if(($deuda->monto_mora ?? 0) > 0)
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Mora</td>
                                                <td style="font-size: 13px; color: #dc2626; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">+ {{ number_format($deuda->monto_mora, 2, ',', '.') }} Bs</td>
                                            </tr>
                                            @endif
                                            @if(($deuda->monto_interes ?? 0) > 0)
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Intereses</td>
                                                <td style="font-size: 13px; color: #dc2626; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">+ {{ number_format($deuda->monto_interes, 2, ',', '.') }} Bs</td>
                                            </tr>
                                            @endif
                                            @if(($deuda->monto_descuento ?? 0) > 0)
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Descuento</td>
                                                <td style="font-size: 13px; color: #16a34a; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">- {{ number_format($deuda->monto_descuento, 2, ',', '.') }} Bs</td>
                                            </tr>
                                            @endif
                                        </table>

                                        <!-- Total -->
                                        <table width="100%" cellpadding="12" cellspacing="0" style="margin-top: 10px; background-color: #1e293b; border-radius: 6px;">
                                            <tr>
                                                <td style="font-size: 15px; color: #ffffff; font-weight: bold;">SALDO A PAGAR</td>
                                                <td style="font-size: 20px; color: #ffffff; font-weight: bold; text-align: right;">{{ number_format($deuda->saldo, 2, ',', '.') }} Bs</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Nota -->
                    <tr>
                        <td style="padding: 15px 30px 30px;">
                            <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.6;">
                                Le recordamos que el pago oportuno de su cuota de condominio contribuye al buen mantenimiento de las areas comunes. Si ya realizo el pago, por favor haga caso omiso a este correo.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                                Este es un correo generado automaticamente. No responda a este mensaje.
                            </p>
                            <p style="font-size: 12px; color: #94a3b8; margin: 5px 0 0;">
                                {{ $companiaNombre }} - Sistema de Administracion de Condominios
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
