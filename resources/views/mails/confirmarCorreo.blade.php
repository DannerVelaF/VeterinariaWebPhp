<!DOCTYPE html>
<html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Código de verificación</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 20px;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .email-container {
                max-width: 600px;
                width: 100%;
                background: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }

            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 40px 30px;
                text-align: center;
                position: relative;
            }

            .header::after {
                content: '';
                position: absolute;
                bottom: -20px;
                left: 0;
                right: 0;
                height: 40px;
                background: #ffffff;
                border-radius: 50% 50% 0 0;
            }

            .logo-container {
                width: 100px;
                height: 100px;
                background: #ffffff;
                border-radius: 50%;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                position: relative;
                z-index: 1;
            }

            .logo-container img {
                width: 70px;
                height: 70px;
                border-radius: 50%;
                object-fit: cover;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                fill: #667eea;
            }

            .header h1 {
                color: #ffffff;
                font-size: 28px;
                margin-bottom: 10px;
                position: relative;
                z-index: 1;
            }

            .header p {
                color: rgba(255, 255, 255, 0.9);
                font-size: 16px;
                position: relative;
                z-index: 1;
            }

            .content {
                padding: 50px 40px 40px;
                text-align: center;
            }

            .welcome-text {
                font-size: 20px;
                color: #333333;
                margin-bottom: 15px;
                font-weight: 600;
            }

            .description {
                color: #666666;
                font-size: 15px;
                line-height: 1.6;
                margin-bottom: 30px;
            }

            .code-container {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                border-radius: 15px;
                padding: 30px;
                margin: 30px 0;
                border: 2px dashed #667eea;
                position: relative;
            }

            .code-label {
                font-size: 14px;
                color: #666666;
                margin-bottom: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 600;
            }

            .verification-code {
                font-size: 48px;
                font-weight: 700;
                color: #667eea;
                letter-spacing: 12px;
                margin: 15px 0;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            }

            .code-info {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                margin-top: 15px;
                color: #888888;
                font-size: 13px;
            }

            .timer-icon {
                width: 16px;
                height: 16px;
            }

            .divider {
                height: 1px;
                background: linear-gradient(to right, transparent, #e0e0e0, transparent);
                margin: 30px 0;
            }

            .instructions {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 20px;
                border-radius: 8px;
                text-align: left;
                margin: 25px 0;
            }

            .instructions h3 {
                color: #333333;
                font-size: 16px;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .instructions ul {
                list-style: none;
                padding: 0;
            }

            .instructions li {
                color: #666666;
                font-size: 14px;
                margin-bottom: 8px;
                padding-left: 25px;
                position: relative;
            }

            .instructions li::before {
                content: '✓';
                position: absolute;
                left: 0;
                color: #667eea;
                font-weight: bold;
            }

            .warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
                text-align: left;
            }

            .warning p {
                color: #856404;
                font-size: 13px;
                margin: 0;
                display: flex;
                align-items: flex-start;
                gap: 10px;
            }

            .warning-icon {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
                margin-top: 2px;
            }

            .footer {
                background: #f8f9fa;
                padding: 30px 40px;
                text-align: center;
                border-top: 1px solid #e0e0e0;
            }

            .footer-text {
                color: #888888;
                font-size: 13px;
                line-height: 1.6;
                margin-bottom: 15px;
            }

            .social-links {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 20px;
            }

            .social-link {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #667eea;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                transition: transform 0.3s ease, background 0.3s ease;
            }

            .social-link:hover {
                transform: translateY(-3px);
                background: #764ba2;
            }

            .social-icon {
                width: 18px;
                height: 18px;
                fill: white;
            }

            .copyright {
                color: #aaaaaa;
                font-size: 12px;
                margin-top: 20px;
            }

            @media only screen and (max-width: 600px) {
                .email-container {
                    border-radius: 10px;
                }

                .content {
                    padding: 40px 25px 30px;
                }

                .header {
                    padding: 30px 20px;
                }

                .verification-code {
                    font-size: 36px;
                    letter-spacing: 8px;
                }

                .footer {
                    padding: 25px 20px;
                }
            }
        </style>
    </head>

    <body>
        <div class="email-container">
            <!-- Header -->
            <div class="header">
                <div class="logo-container">
                    <!-- Si tienes logo, usa: <img src="{{ $message->embed(public_path('images/logo.jpg')) }}" alt="Logo"> -->
                    <!-- Si no, usa el ícono SVG: -->
                    <svg class="logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="currentColor" opacity="0.3" />
                        <path d="M2 17L12 22L22 17V7L12 12L2 7V17Z" fill="currentColor" />
                    </svg>
                </div>
                <h1>VeterinariaWeb</h1>
                <p>Sistema de Gestión Veterinaria</p>
            </div>

            <!-- Content -->
            <div class="content">
                <h2 class="welcome-text">¡Bienvenido!</h2>
                <p class="description">
                    Gracias por registrarte en VeterinariaWeb. Para completar tu registro y verificar tu cuenta,
                    utiliza el siguiente código de verificación:
                </p>

                <div class="code-container">
                    <div class="code-label">Código de Verificación</div>
                    <div class="verification-code">{{ $code }}</div>
                    <div class="code-info">
                        <svg class="timer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="#888888" stroke-width="2" />
                            <path d="M12 6V12L16 14" stroke="#888888" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>Este código expira en 15 minutos</span>
                    </div>
                </div>

                <div class="instructions">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V11H13V17ZM13 9H11V7H13V9Z"
                                fill="#667eea" />
                        </svg>
                        Instrucciones
                    </h3>
                    <ul>
                        <li>Copia el código de 4 dígitos mostrado arriba</li>
                        <li>Regresa a la página de registro</li>
                        <li>Ingresa el código en el campo de verificación</li>
                        <li>Haz clic en "Confirmar" para activar tu cuenta</li>
                    </ul>
                </div>

                <div class="warning">
                    <p>
                        <svg class="warning-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 22H22L12 2Z" fill="#ffc107" />
                            <path d="M12 17H12.01M12 10V14" stroke="#856404" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>
                            <strong>Importante:</strong> Si no solicitaste este código, por favor ignora este mensaje.
                            Tu cuenta permanecerá segura y no se realizarán cambios.
                        </span>
                    </p>
                </div>

                <div class="divider"></div>

                <p class="description" style="margin-bottom: 0;">
                    ¿Necesitas ayuda? No dudes en contactarnos. Estamos aquí para asistirte.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p class="footer-text">
                    Este correo fue enviado automáticamente. Por favor, no respondas a este mensaje.
                </p>
                <p class="footer-text">
                    <strong>VeterinariaWeb</strong><br>
                    Sistema de Gestión y Cuidado Animal
                </p>

                <div class="social-links">
                    <a href="#" class="social-link">
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                        </svg>
                    </a>
                    <a href="#" class="social-link">
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                </div>

                <p class="copyright">
                    © 2024 VeterinariaWeb. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </body>

</html>
