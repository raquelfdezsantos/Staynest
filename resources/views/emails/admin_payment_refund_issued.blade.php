@extends('emails.layouts.staynest')

@section('title', 'Devolución completada')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #2c5aa0; font-size: 20px;">Devolución completada - Reserva {{ $reservation->code ?? ('#'.$reservation->id) }}</h2>

<p style="margin: 0 0 16px 0;">Hola {{ $reservation->property->user->name }},</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0 0 8px 0; font-weight: bold;">Se ha procesado una devolución</p>
    <p style="margin: 0; font-size: 24px; font-weight: bold; color: #dc3545;">{{ number_format($refundAmount, 2) }}€</p>
</div>

<h3 style="margin: 24px 0 12px 0; font-size: 16px; color: #333;">Detalles de la reserva:</h3>

<table style="width: 100%; margin: 12px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Reserva ID:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">#{{ $reservation->id }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Cliente:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Propiedad:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->property->name }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Check-in:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_in->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Check-out:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_out->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; {{ $invoice ? 'border-bottom: 1px solid #e9ecef;' : '' }}"><strong>Total actual:</strong></td>
        <td style="padding: 12px; {{ $invoice ? 'border-bottom: 1px solid #e9ecef;' : '' }} text-align: right;">{{ number_format($reservation->total_price, 2) }}€</td>
    </tr>
    @if($invoice)
    <tr>
        <td style="padding: 12px;"><strong>Factura rectificativa:</strong></td>
        <td style="padding: 12px; text-align: right;">{{ $invoice->number }}</td>
    </tr>
    @endif
</table>

@if($invoice)
<p style="margin: 16px 0; color: #666; font-size: 14px;">La factura rectificativa está adjunta en este correo en formato PDF.</p>
@endif

<p style="margin: 20px 0 0 0; font-size: 14px; color: #666;">Esta devolución se ha procesado correctamente.@if($invoice) Se ha generado una factura rectificativa que se adjunta a este correo.@endif</p>
@endsection
