<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Código de verificación</title>
    </head>

    <body
        style="margin:0; padding:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background:#f0f2f5; padding:20px;">

        <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:500px; margin:auto;">
            <tr>
                <td>
                    <!-- Tarjeta principal -->
                    <table width="100%" cellpadding="0" cellspacing="0"
                        style="background:#ffffff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); overflow:hidden;">

                        <!-- Header -->
                        <tr>
                            <td
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:25px 20px; text-align:center;">

                                <!-- Logo -->
                                <div
                                    style="background:#ffffff; width:60px; height:60px; border-radius:50%; margin:0 auto 12px; display:inline-block; box-shadow:0 2px 10px rgba(0,0,0,0.15); overflow:hidden; line-height:60px;">
                                    <img src="{{ $message->embed(public_path('images/logo.jpg')) }}" alt="Logo"
                                        style="max-height:45px; vertical-align:middle;">
                                </div>

                                <h1 style="margin:0; color:#ffffff; font-size:20px; font-weight:600;">
                                    Verificación de Seguridad
                                </h1>
                            </td>
                        </tr>

                        <!-- Contenido -->
                        <tr>
                            <td style="padding:30px 25px; text-align:center;">
                                <h2 style="margin:0 0 10px 0; color:#1a202c; font-size:18px; font-weight:600;">
                                    ¡Hola! 👋
                                </h2>

                                <p style="font-size:14px; line-height:1.5; color:#4a5568; margin:0 0 20px 0;">
                                    Usa el siguiente código para continuar:
                                </p>

                                <!-- Código -->
                                <div
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:10px; padding:2px; margin:0 auto 20px; max-width:220px; box-shadow:0 4px 15px rgba(102, 126, 234, 0.25);">
                                    <div style="background:#ffffff; border-radius:8px; padding:18px 15px;">
                                        <div
                                            style="color:#667eea; font-size:32px; font-weight:700; letter-spacing:6px; font-family: 'Courier New', monospace;">
                                            {{ $code }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div
                                    style="background:#f7fafc; border-left:3px solid #667eea; border-radius:6px; padding:12px 15px; text-align:left; margin:0 0 15px 0;">
                                    <p style="margin:0; color:#718096; font-size:12px; line-height:1.5;">
                                        • Válido por <strong>10 minutos</strong><br>
                                        • No compartas este código<br>
                                        • Si no lo solicitaste, ignóralo
                                    </p>
                                </div>

                                <p style="font-size:11px; color:#a0aec0; margin:0;">
                                    Nunca te pediremos este código por teléfono o email
                                </p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td
                                style="background:#f7fafc; padding:20px; text-align:center; border-top:1px solid #e2e8f0;">
                                <p style="margin:0 0 5px 0; color:#4a5568; font-size:13px; font-weight:600;">
                                    🚀 Gracias por confiar en nosotros
                                </p>
                                <p style="margin:0; font-size:11px; color:#a0aec0;">
                                    © 2025 Tu Empresa. Todos los derechos reservados.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

    </body>

</html>
