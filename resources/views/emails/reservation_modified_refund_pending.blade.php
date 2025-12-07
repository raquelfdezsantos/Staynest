@extends('emails.layouts.staynest')

@section('title', 'Reserva modificada')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #2c5aa0; font-size: 20px;">Reserva {{ $reservation->code ?? ('#'.$reservation->id) }} modificada</h2>

@if($isAdmin ?? false)
  <p style="margin: 0 0 16px 0;">Hola {{ $reservation->property->user->name }},</p>
  <p style="margin: 0 0 20px 0;">Te informamos que la reserva de <strong>{{ $reservation->user->name }}</strong> ({{ $reservation->user->email }}) en <strong>{{ $reservation->property->name }}</strong> ha sido modificada.</p>
@else
  <p style="margin: 0 0 16px 0;">Hola {{ $reservation->user->name }},</p>
  <p style="margin: 0 0 20px 0;">Tu reserva <strong>#{{ $reservation->id }}</strong> en <strong>{{ $reservation->property->name }}</strong> ha sido modificada correctamente.</p>
@endif

<h3 style="margin: 24px 0 12px 0; font-size: 16px; color: #333;">Nuevos detalles de la reserva:</h3>

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
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef;"><strong>Huéspedes:</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e9ecef; text-align: right;">{{ $reservation->guests }}</td>
    </tr>
    <tr>
        <td style="padding: 12px;"><strong>Nuevo total:</strong></td>
        <td style="padding: 12px; text-align: right; font-weight: bold;">{{ number_format($newTotal, 2) }}€</td>
    </tr>
</table>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; margin: 20px 0; border-radius: 2px;">
    <p style="margin: 0 0 8px 0; font-weight: bold;">Devolución pendiente</p>
    @if($isAdmin ?? false)
      <p style="margin: 0 0 12px 0;">El nuevo total de la reserva es inferior al monto ya pagado. Procederemos a tramitar la devolución de:</p>
      <p style="margin: 0 0 12px 0; font-size: 24px; font-weight: bold; color: #28a745;">{{ number_format($refundAmount, 2) }}€</p>
      <p style="margin: 0;">Enviaremos un correo de confirmación al cliente cuando la devolución se haya procesado correctamente.</p>
    @else
      <p style="margin: 0 0 12px 0;">El nuevo total de la reserva es inferior al monto ya pagado. Procederemos a tramitar la devolución de:</p>
      <p style="margin: 0 0 12px 0; font-size: 24px; font-weight: bold; color: #28a745;">{{ number_format($refundAmount, 2) }}€</p>
      <p style="margin: 0;">Te enviaremos un correo de confirmación cuando la devolución se haya procesado correctamente.</p>
    @endif
</div>

@if($invoice)
<div style="text-align: center; margin: 24px 0;">
    <p style="color:#666; font-size:14px; margin:0;">La factura actualizada está adjunta en este correo en formato PDF.</p>
</div>
@endif

<p style="margin: 20px 0 0 0;">Si tienes alguna pregunta, no dudes en contactarnos.</p>
@endsection
