@extends('emails.layouts.staynest')

@section('title', 'Reserva cancelada')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #dc3545; font-size: 20px;">Reserva cancelada en tu propiedad</h2>

<p style="margin: 0 0 16px 0;">Hola {{ $reservation->property->user->name }},</p>

<p style="margin: 0 0 20px 0;">Te informamos que la reserva en <strong>{{ $reservation->property->name }}</strong> ha sido cancelada.</p>

<h3 style="margin: 24px 0 12px 0; font-size: 16px; color: #333;">Detalles de la reserva cancelada:</h3>

<table style="width: 100%; margin: 20px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Cliente:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
    </tr>
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
<p style="margin: 16px 0; color: #666; font-size: 14px;">La factura rectificativa está adjunta en este correo en formato PDF.</p>
@endif

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0; color: #856404;">Las noches han sido liberadas automáticamente en tu calendario y están disponibles para nuevas reservas.</p>
</div>
@endsection
