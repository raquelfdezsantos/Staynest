@extends('emails.layouts.staynest')

@section('title', 'Propiedad dada de baja')

@section('content')
<h2 style="margin: 0 0 20px 0; color: #dc2626; font-size: 20px;">Propiedad dada de baja correctamente</h2>

<p style="margin: 0 0 16px 0;">Hola,</p>

<p style="margin: 0 0 20px 0;">Tu propiedad <strong>{{ $propertyName }}</strong> ha sido dada de baja exitosamente.</p>

@if($cancelledReservations > 0)
    <div style="background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 16px; margin: 20px 0; border-radius: 2px;">
        <h3 style="margin: 0 0 12px 0; color: #991b1b; font-size: 16px;">Resumen de cancelaciones</h3>
        <table style="width: 100%; margin: 10px 0;">
            <tr>
                <td style="padding: 6px 0;"><strong>Reservas canceladas:</strong></td>
                <td style="padding: 6px 0; text-align: right;">{{ $cancelledReservations }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0;"><strong>Total reembolsado:</strong></td>
                <td style="padding: 6px 0; text-align: right;">{{ number_format($totalRefunded, 2, ',', '.') }} €</td>
            </tr>
        </table>
        <p style="margin: 12px 0 0 0; font-size: 14px; color: #6b7280;">
            Los clientes afectados recibirán una notificación por correo electrónico con los detalles de la cancelación y el reembolso.
        </p>
    </div>
@else
    <div style="background-color: #d1fae5; border-left: 4px solid #059669; padding: 16px; margin: 20px 0; border-radius: 2px;">
        <p style="margin: 0; color: #065f46;">No había reservas futuras activas para esta propiedad.</p>
    </div>
@endif

<p style="margin: 20px 0; font-size: 14px; color: #6b7280;">
    Esta acción es reversible desde la base de datos si necesitas recuperar la propiedad.
</p>
@endsection
