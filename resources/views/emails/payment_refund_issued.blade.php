<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolución completada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #d4edda; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #e9ecef; border-radius: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #6c757d; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✅ Devolución completada - Reserva {{ $reservation->code ?? ('#'.$reservation->id) }}</h2>
        </div>
        
        <div class="content">
            <p>Hola {{ $reservation->user->name }},</p>
            
            <div class="success">
                <p><strong>La devolución se ha procesado correctamente</strong></p>
                <p class="amount">{{ number_format(abs($refund), 2) }}€</p>
            </div>
            
            <p>Esta devolución está relacionada con la modificación de tu reserva <strong>#{{ $reservation->id }}</strong> en <strong>{{ $reservation->property->name }}</strong>.</p>
            
            <h3>Detalles de la reserva:</h3>
            <ul>
                <li><strong>Check-in:</strong> {{ $reservation->check_in->format('d/m/Y') }}</li>
                <li><strong>Check-out:</strong> {{ $reservation->check_out->format('d/m/Y') }}</li>
                <li><strong>Total actual:</strong> {{ number_format($reservation->total_price, 2) }}€</li>
                @if($invoice)
                <li><strong>Factura rectificativa:</strong> {{ $invoice->number }} (adjunta en este correo)</li>
                @endif
            </ul>
            
            @if($invoice)
            <p style="margin: 20px 0;">
                <a href="{{ route('invoices.show', $invoice->number) }}" style="display: inline-block; padding: 12px 24px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    📄 Ver Factura Rectificativa Online
                </a>
            </p>
            @endif
            
            <p>El importe devuelto debería aparecer en tu cuenta en los próximos 5-10 días hábiles, dependiendo de tu entidad bancaria. Encontrarás la factura rectificativa adjunta a este correo y también puedes acceder a ella en cualquier momento a través del enlace superior.</p>
            
            <p>Si tienes alguna pregunta, contacta con nosotros respondiendo a este correo.</p>
            
            <p>Saludos cordiales,<br>El equipo de {{ config('app.name') }}</p>
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático. Si tienes dudas, responde a este correo o contacta con {{ config('mail.from.address') }}</p>
        </div>
    </div>
</body>
</html>
