<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Servicio Recibida</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f5f7; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f5f7; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #273272, #680c3e); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 22px; margin: 0;">Administradora Integral E.L.B., C.A</h1>
                            <p style="color: #cbd5e1; font-size: 13px; margin: 8px 0 0;">Solicitud de Servicio Recibida</p>
                        </td>
                    </tr>

                    <!-- Saludo -->
                    <tr>
                        <td style="padding: 30px 30px 15px;">
                            <p style="font-size: 15px; color: #334155; margin: 0;">
                                Estimado(a) <strong>{{ $solicitud->nombres_apellidos }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #64748b; margin: 12px 0 0; line-height: 1.6;">
                                Hemos recibido su solicitud de servicio satisfactoriamente. En la brevedad posible uno de nuestros representantes se comunicará con usted para dar respuesta a su requerimiento.
                            </p>
                        </td>
                    </tr>

                    <!-- Detalle de la solicitud -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #273272; font-size: 15px; margin: 0 0 15px; border-bottom: 2px solid #680c3e; padding-bottom: 8px;">
                                            Resumen de su Solicitud
                                        </h3>
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0; width: 35%;">Número de Solicitud:</td>
                                                <td style="font-size: 13px; color: #273272; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">#{{ $solicitud->id }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Asunto:</td>
                                                <td style="font-size: 13px; color: #1e293b; font-weight: bold; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $solicitud->asunto }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Fecha:</td>
                                                <td style="font-size: 13px; color: #1e293b; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $solicitud->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Teléfono:</td>
                                                <td style="font-size: 13px; color: #1e293b; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ $solicitud->telefono }}</td>
                                            </tr>
                                            @if($solicitud->descripcion)
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; vertical-align: top; padding-top: 10px;">Descripción:</td>
                                                <td style="font-size: 13px; color: #1e293b; text-align: right; padding-top: 10px;">{{ $solicitud->descripcion }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Nota -->
                    <tr>
                        <td style="padding: 15px 30px 30px;">
                            <div style="background-color: #eff6ff; border-left: 4px solid #273272; padding: 15px; border-radius: 0 6px 6px 0;">
                                <p style="font-size: 13px; color: #1e40af; margin: 0; line-height: 1.6;">
                                    <strong>¿Necesita contactarnos urgentemente?</strong><br>
                                    Puede comunicarse directamente con nuestras oficinas. Tenga a mano su número de solicitud <strong>#{{ $solicitud->id }}</strong>.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                                Este es un correo generado automáticamente. No responda a este mensaje.
                            </p>
                            <p style="font-size: 12px; color: #94a3b8; margin: 5px 0 0;">
                                Administradora Integral E.L.B., C.A — Sistema de Administración de Condominios
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
