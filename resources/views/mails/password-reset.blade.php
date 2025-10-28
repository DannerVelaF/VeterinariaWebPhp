{{-- resources/views/emails/password-reset.blade.php --}}
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Restablecer Contraseña - ADELA VETERINARIA & SPA</title>
        <style>
            .container {
                max-width: 600px;
                margin: 0 auto;
                font-family: Arial, sans-serif;
            }

            .header {
                background: linear-gradient(135deg, #fd4c82, #e63b6f);
                color: white;
                padding: 20px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }

            .content {
                padding: 30px;
                background: #f9f9f9;
            }

            .button {
                background-color: #fd4c82;
                color: white;
                padding: 12px 30px;
                text-decoration: none;
                border-radius: 6px;
                display: inline-block;
                font-weight: bold;
                margin: 20px 0;
            }

            .warning {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 6px;
                padding: 15px;
                margin: 20px 0;
            }

            .footer {
                text-align: center;
                padding: 20px;
                color: #666;
                font-size: 12px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <h1>ADELA VETERINARIA & SPA</h1>
                <h2>Restablecer Contraseña</h2>
            </div>

            <div class="content">
                <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>

                <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>

                <div style="text-align: center;">
                    <a href="{{ $resetLink }}" class="button">
                        Restablecer Contraseña
                    </a>
                </div>

                <div class="warning">
                    <p><strong>⚠️ Información importante:</strong></p>
                    <ul>
                        <li>Este enlace es de <strong>un solo uso</strong></li>
                        <li>Caduca en <strong>1 hora</strong></li>
                        <li>Si no solicitaste este cambio, puedes ignorar este mensaje</li>
                    </ul>
                </div>

                <p>Si el botón no funciona, copia y pega esta URL en tu navegador:</p>
                <p style="word-break: break-all; color: #666; background: #fff; padding: 10px; border-radius: 4px;">
                    {{ $resetLink }}
                </p>
            </div>

            <div class="footer">
                <p>© {{ date('Y') }} ADELA VETERINARIA & SPA. Todos los derechos reservados.</p>
                <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            </div>
        </div>
    </body>

</html>
