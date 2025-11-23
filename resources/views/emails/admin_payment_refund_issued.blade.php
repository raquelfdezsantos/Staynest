<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Devolución completada (Admin)</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #fff3cd; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #e9ecef; border-radius: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 12px; color: #6c757d; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #0066cc; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Devolución completada - Reserva {{ $reservation->code ?? ('#'.$reservation->id) }}</h2>
        </div>
        
        <div class="content">
            <p>Hola Admin,</p>
            
            <div class="info">
                <p><strong>Se ha procesado una devolución</strong></p>
                <p class="amount">{{ number_format($refundAmount, 2) }}€</p>
            </div>
            
            <h3>Detalles de la reserva:</h3>
            <ul>
                <li><strong>Reserva ID:</strong> #{{ $reservation->id }}</li>
                <li><strong>Cliente:</strong> {{ $reservation->user->name }} ({{ $reservation->user->email }})</li>
                <li><strong>Propiedad:</strong> {{ $reservation->property->name }}</li>
                <li><strong>Check-in:</strong> {{ $reservation->check_in->format('d/m/Y') }}</li>
                <li><strong>Check-out:</strong> {{ $reservation->check_out->format('d/m/Y') }}</li>
                <li><strong>Total actual:</strong> {{ number_format($reservation->total_price, 2) }}€</li>
                @if($invoice)
                <li><strong>Factura rectificativa:</strong> {{ $invoice->number }}</li>
                @endif
            </ul>
            
            @if($invoice)
            <p style="margin: 20px 0;">
                <a href="{{ route('invoices.show', $invoice->number) }}" style="display: inline-block; padding: 12px 24px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    📄 Ver Factura Rectificativa Online
                </a>
            </p>
            @endif
            
            <p>Esta devolución se ha procesado y se ha generado una factura rectificativa que se adjunta a este correo y está disponible a través del enlace superior.</p>
            
            <p><small>Este es un mensaje automático del sistema de gestión de reservas.</small></p>
        </div>
        
        <div class="footer">
            <p>Sistema de notificaciones automáticas - {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
