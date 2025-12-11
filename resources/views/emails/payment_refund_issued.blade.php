@extends('emails.layouts.staynest')

@section('title', 'Devolución completada')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #28a745; font-size: 20px;">Devolución completada - Reserva {{ $reservation->code ?? ('#'.$reservation->id) }}</h2>

<p style="margin: 0 0 16px 0;">Hola {{ $reservation->user->name }},</p>

<div style="background: #d4edda; border-left: 4px solid #28a745; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0 0 8px 0; font-weight: bold;">La devolución se ha procesado correctamente</p>
    <p style="margin: 0; font-size: 24px; font-weight: bold; color: #28a745;">{{ number_format(abs($refund), 2) }}€</p>
</div>

<p style="margin: 0 0 20px 0;">Esta devolución está relacionada con la modificación de tu reserva <strong>#{{ $reservation->id }}</strong> en <strong>{{ $reservation->property->name }}</strong>.</p>

<h3 style="margin: 24px 0 12px 0; font-size: 16px; color: #333;">Detalles de la reserva:</h3>

<table style="width: 100%; margin: 12px 0; border-collapse: collapse; background: #f8f9fa; border-radius: 2px; overflow: hidden;">
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Check-in:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_in->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Check-out:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->check_out->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td style="padding: 12px; @if($invoice) border-bottom: 1px solid #e9ecef; @endif"><strong>Total actual:</strong></td>
        <td style="padding: 12px; @if($invoice) border-bottom: 1px solid #e9ecef; @endif text-align: right;">{{ number_format($reservation->total_price, 2) }}€</td>
    </tr>
    @if($invoice)
    <tr>
        <td style="padding: 12px;"><strong>Factura rectificativa:</strong></td>
        <td style="padding: 12px; text-align: right;">{{ $invoice->number }} (adjunta en este correo)</td>
    </tr>
    @endif
</table>

<p style="margin: 20px 0;">El importe devuelto debería aparecer en tu cuenta en los próximos 5-10 días hábiles, dependiendo de tu entidad bancaria.@if($invoice) Encontrarás la factura rectificativa adjunta a este correo.@endif</p>

<p style="margin: 20px 0 0 0;">Si tienes alguna pregunta, contacta con nosotros respondiendo a este correo.</p>
@endsection
