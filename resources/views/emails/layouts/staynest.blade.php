<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Staynest' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f5f5f5; color: #1a1a1a;">
    <!-- Wrapper principal -->
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f5f5f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Container principal -->
                <table cellpadding="0" cellspacing="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    
                    <!-- Header con logo -->
                    <tr>
                        <td style="background-color: #2a2a2a; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; font-family: Georgia, serif; font-size: 32px; color: #ffffff; font-weight: 400; letter-spacing: 0.5px;">Staynest</h1>
                        </td>
                    </tr>
                    
                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2a2a2a; padding: 30px 40px; text-align: center; border-top: 1px solid #3a3a3a;">
                            <p style="margin: 0 0 12px; font-size: 13px; color: #999999;">
                                &copy; {{ date('Y') }} Staynest. Todos los derechos reservados.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #777777;">
                                Este es un correo electrónico automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
