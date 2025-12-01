@extends('emails.layouts.staynest')

@section('title', 'Pago recibido')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #2c5aa0; font-size: 20px;">Pago confirmado por el cliente</h2>

<p style="margin: 0 0 16px 0;">Hola {{ $reservation->property->user->name }},</p>

<p style="margin: 0 0 20px 0;">Te informamos que se ha recibido un pago para una reserva de tu propiedad.</p>

<table style="width: 100%; margin: 20px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Factura:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $invoice->number }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Cliente:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Alojamiento:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->property->name ?? 'Alojamiento' }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Fechas:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Importe:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right; font-weight: bold; color: #4CAF50; font-size: 18px;">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
    </tr>
    <tr>
        <td style="padding: 12px;"><strong>Estado reserva:</strong></td>
        <td style="padding: 12px; text-align: right;">{{ ucfirst($reservation->status) }}</td>
    </tr>
</table>

<div style="text-align: center; margin: 24px 0;">
    <p style="color:#666; font-size:14px; margin:0;">La factura está adjunta en este correo en formato PDF.</p>
</div>
@endsection
