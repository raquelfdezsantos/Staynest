@extends('emails.layouts.staynest')

@section('content')
  <h2 style="margin: 0 0 20px; font-family: Georgia, serif; font-size: 24px; color: #ff9800; font-weight: 500;">Tu reserva expira pronto</h2>
  
  <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #333333;">Hola {{ $reservation->user->name }},</p>
  <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #333333;">Te recordamos que tu reserva está pendiente de pago y <strong>expirará en aproximadamente 1 minuto</strong>.</p>

  <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; background-color: #f9f9f9; border-radius: 6px;"
    <tr><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Reserva:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->code ?? ('#'.$reservation->id) }}</td></tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Alojamiento:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->property->name ?? 'Alojamiento' }}</td></tr>
    <tr><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Entrada:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->check_in->format('d/m/Y') }}</td></tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Salida:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->check_out->format('d/m/Y') }}</td></tr>
    <tr><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Total:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333; font-weight: 600;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td></tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Expira:</strong></td><td style="padding: 10px; font-size: 14px; color: #ff9800; font-weight: 600;">{{ $reservation->expires_at->format('d/m/Y \a \l\a\s H:i') }}</td></tr>
  </table>

  <p style="margin: 24px 0 0; padding: 16px; background-color: #fff3cd; border-left: 3px solid #ff9800; border-radius: 4px; font-size: 14px; line-height: 1.6; color: #333333;">
    <strong>¡Atención!</strong> Si no completas el pago antes de la hora de expiración, tu reserva será cancelada automáticamente y las fechas quedarán disponibles para otros huéspedes.
  </p>

  <div style="margin: 32px 0; text-align: center;">
    <a href="{{ route('properties.reservas.index', $reservation->property->slug) }}" style="display: inline-block; padding: 14px 32px; background-color: #4D8D94; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: 500;">
      Completar pago ahora
    </a>
  </div>
  
  <p style="margin: 24px 0 0; font-size: 15px; line-height: 1.6; color: #333333;">Si tienes alguna duda o problema para completar el pago, contáctanos lo antes posible.</p>
@endsection
