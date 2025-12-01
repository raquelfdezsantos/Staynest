@extends('emails.layouts.staynest')

@section('title', 'Reserva cancelada')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #dc3545; font-size: 20px;">Reserva cancelada</h2>

<p style="margin: 0 0 16px 0;">Hola {{ $reservation->user->name }},</p>

<p style="margin: 0 0 20px 0;">Tu reserva en <strong>{{ $reservation->property->name }}</strong> ha sido cancelada correctamente.</p>

<table style="width: 100%; margin: 20px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Nº de huéspedes:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->guests }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Fechas:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px;"><strong>Total:</strong></td>
        <td style="padding: 12px; text-align: right;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
    </tr>
</table>

@if($invoice)
<div style="text-align: center; margin: 24px 0;">
    <p style="color:#666; font-size:14px; margin:0;">La factura rectificativa está adjunta en este correo en formato PDF.</p>
</div>
@endif

<div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0; color: #721c24;">Si tienes dudas, contacta con soporte.</p>
</div>
@endsection
