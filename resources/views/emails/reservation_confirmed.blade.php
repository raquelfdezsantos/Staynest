@extends('emails.layouts.staynest')

@section('content')
  <h2 style="margin: 0 0 20px; font-family: Georgia, serif; font-size: 24px; color: #4D8D94; font-weight: 500;">¡Tu reserva se ha registrado!</h2>
  
  <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #333333;">Hola {{ $reservation->user->name }},</p>
  <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #333333;">Hemos recibido tu solicitud de reserva. Detalles:</p>

  <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; background-color: #f9f9f9; border-radius: 6px;"
    <tr><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Reserva:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->code ?? ('#'.$reservation->id) }}</td></tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Alojamiento:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->property->name ?? 'Alojamiento' }}</td></tr>
    <tr><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Entrada:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->check_in->format('d/m/Y') }}</td></tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Salida:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333;">{{ $reservation->check_out->format('d/m/Y') }}</td></tr>
    <tr>
      <td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Huéspedes:</strong></td>
      <td style="padding: 10px; font-size: 14px; color: #333333;">
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
          {{ implode(', ', $parts) }} (total: {{ $reservation->guests }})
        @else
          {{ $reservation->guests }}
        @endif
      </td>
    </tr>
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Total:</strong></td><td style="padding: 10px; font-size: 14px; color: #333333; font-weight: 600;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td></tr>
    @if(!empty($reservation->notes))
      <tr>
        <td valign="top" style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Notas del huésped:</strong></td>
        <td style="padding: 10px; font-size: 14px; color: #333333; white-space: pre-wrap;">{{ $reservation->notes }}</td>
      </tr>
    @endif
    <tr style="background-color: #ffffff;"><td style="padding: 10px; font-size: 14px; color: #666666;"><strong style="color: #333333;">Estado:</strong></td><td style="padding: 10px; font-size: 14px; color: #4D8D94; font-weight: 500;">{{ ucfirst($reservation->status) }}</td></tr>
  </table>

  <p style="margin: 24px 0 0; padding: 16px; background-color: #f0f8f9; border-left: 3px solid #4D8D94; border-radius: 4px; font-size: 14px; line-height: 1.6; color: #333333;">
    Cuando completes el pago te enviaremos la factura automáticamente.
  </p>
  
  <p style="margin: 24px 0 0; font-size: 15px; line-height: 1.6; color: #333333;">Gracias por tu reserva.</p>
@endsection
