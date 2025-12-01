@extends('emails.layouts.staynest')

@section('title', 'Pago de diferencia completado')

@section('content')
<p style="margin: 0 0 16px 0;">Hola {{ $reservation->property->user->name }},</p>

<p style="margin: 0 0 20px 0;">Te informamos que el cliente <strong>{{ $reservation->user->name }}</strong> ha completado el pago de la diferencia correspondiente a la reserva modificada.</p>

<table style="width: 100%; margin: 20px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>ID Reserva:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">#{{ $reservation->id }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Propiedad:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->property->name }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Cliente:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Check-in:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px;"><strong>Check-out:</strong></td>
        <td style="padding: 12px; text-align: right;">{{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
    </tr>
</table>

<div style="background: #e8f5e9; border-left: 4px solid #4CAF50; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0 0 8px 0; font-weight: bold; color: #2e7d32;">Importe abonado:</p>
    <p style="margin: 0; font-size: 24px; font-weight: bold; color: #4CAF50;">{{ number_format($amount, 2, ',', '.') }} €</p>
</div>

@if($invoice)
<p style="margin: 16px 0; color: #666; font-size: 14px;">La factura está adjunta en este correo en formato PDF.</p>
@endif

<p style="margin: 20px 0 0 0;">El pago se ha procesado correctamente.</p>
@endsection
