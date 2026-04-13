<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta a su Solicitud</title>
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
                            <p style="color: #cbd5e1; font-size: 13px; margin: 8px 0 0;">Respuesta a su Solicitud de Servicio</p>
                        </td>
                    </tr>

                    <!-- Saludo -->
                    <tr>
                        <td style="padding: 30px 30px 15px;">
                            <p style="font-size: 15px; color: #334155; margin: 0;">
                                Estimado(a) <strong>{{ $solicitud->nombres_apellidos }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #64748b; margin: 12px 0 0; line-height: 1.6;">
                                En atención a su solicitud <strong>#{{ $solicitud->id }}</strong> con asunto <strong>"{{ $solicitud->asunto }}"</strong>, le informamos lo siguiente:
                            </p>
                        </td>
                    </tr>

                    <!-- Cuerpo del mensaje -->
                    <tr>
                        <td style="padding: 15px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #273272; font-size: 15px; margin: 0 0 15px; border-bottom: 2px solid #680c3e; padding-bottom: 8px;">
                                            Mensaje
                                        </h3>
                                        <p style="font-size: 14px; color: #334155; line-height: 1.7; margin: 0; white-space: pre-line;">{{ $cuerpoMensaje }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Info solicitud -->
                    <tr>
                        <td style="padding: 10px 30px 20px;">
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                                Referencia: Solicitud #{{ $solicitud->id }} — {{ $solicitud->asunto }} — Enviado el {{ $solicitud->created_at->format('d/m/Y') }}
                            </p>
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
