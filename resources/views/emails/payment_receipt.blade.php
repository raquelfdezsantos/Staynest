<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Pago confirmado</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222; max-width:600px; margin:0 auto; padding:20px;">
  <div style="background:#4caf50; color:white; padding:20px; border-radius:8px 8px 0 0; text-align:center;">
    <h2 style="margin:0; font-size:24px;">✓ Pago confirmado</h2>
    <p style="margin:8px 0 0 0; font-size:14px; opacity:0.9;">Tu pago ha sido procesado exitosamente</p>
  </div>

  <div style="background:#fff; border:2px solid #e0e0e0; border-top:none; border-radius:0 0 8px 8px; padding:24px;">
    <p style="margin:0 0 16px 0; font-size:16px;">Hola <strong>{{ $reservation->user->name }}</strong>,</p>
    <p style="margin:0 0 20px 0; color:#666;">Hemos recibido y procesado tu pago correctamente. A continuación encontrarás el detalle de la transacción:</p>

    <div style="background:#f5f5f5; border-radius:8px; padding:16px; margin:20px 0;">
      <h3 style="margin:0 0 12px 0; font-size:16px; color:#333;">📄 Detalles del pago</h3>
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <td style="padding:8px 0; color:#666; width:40%;"><strong>Nº Factura:</strong></td>
          <td style="padding:8px 0; text-align:right; font-family:monospace; color:#2c5aa0; font-weight:bold;">{{ $invoice->number }}</td>
        </tr>
        <tr style="border-top:1px solid #e0e0e0;">
          <td style="padding:8px 0; color:#666;"><strong>Reserva:</strong></td>
          <td style="padding:8px 0; text-align:right;">{{ $reservation->code ?? '#'.$reservation->id }}</td>
        </tr>
        <tr style="border-top:1px solid #e0e0e0;">
          <td style="padding:8px 0; color:#666;"><strong>Propiedad:</strong></td>
          <td style="padding:8px 0; text-align:right;">{{ $reservation->property->name ?? 'Alojamiento' }}</td>
        </tr>
        <tr style="border-top:1px solid #e0e0e0;">
          <td style="padding:8px 0; color:#666;"><strong>Fechas:</strong></td>
          <td style="padding:8px 0; text-align:right;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
        </tr>
        <tr style="border-top:1px solid #e0e0e0;">
          <td style="padding:8px 0; color:#666;"><strong>Huéspedes:</strong></td>
          <td style="padding:8px 0; text-align:right;">
            @php
              $parts = [];
              $ad = (int) ($reservation->adults ?? 0);
              $ch = (int) ($reservation->children ?? 0);
              $pt = (int) ($reservation->pets ?? 0);
              if ($ad > 0) { $parts[] = $ad.' '.($ad === 1 ? 'adulto' : 'adultos'); }
              if ($ch > 0) { $parts[] = $ch.' '.($ch === 1 ? 'niño' : 'niños'); }
              if ($pt > 0) { $parts[] = $pt.' '.($pt === 1 ? 'mascota' : 'mascotas'); }
            @endphp
            @if(count($parts))
              {{ implode(', ', $parts) }}
            @else
              {{ $reservation->guests }} {{ $reservation->guests === 1 ? 'persona' : 'personas' }}
            @endif
          </td>
        </tr>
        <tr style="border-top:2px solid #4caf50;">
          <td style="padding:12px 0; color:#333; font-size:16px;"><strong>Importe pagado:</strong></td>
          <td style="padding:12px 0; text-align:right; font-size:20px; font-weight:bold; color:#4caf50;">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
        </tr>
      </table>
    </div>

    <div style="background:#e3f2fd; border-left:4px solid #2196f3; padding:12px; margin:20px 0; border-radius:4px;">
      <p style="margin:0; color:#1565c0; font-size:14px;">
        <strong>💳 Método de pago:</strong> {{ ucfirst($reservation->payments->last()->method ?? 'Tarjeta') }}<br>
        <strong>📅 Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>✓ Estado:</strong> Pagado
      </p>
    </div>

    <div style="text-align:center; margin:24px 0;">
      <a href="{{ route('invoices.show', $invoice->number) }}"
         style="display:inline-block; padding:12px 24px; background:#2c5aa0; color:white; text-decoration:none; border-radius:6px; font-weight:bold; font-size:15px;">
        📄 Ver factura completa
      </a>
    </div>

    <hr style="border:none; border-top:1px solid #e0e0e0; margin:24px 0;">
    
    <p style="color:#666; font-size:13px; margin:0; text-align:center;">
      Gracias por confiar en nosotros.<br>
      <strong>Staynest</strong> - Tu alojamiento perfecto
    </p>
  </div>
</body>
</html>
