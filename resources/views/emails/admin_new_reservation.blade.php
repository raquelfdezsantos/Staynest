@extends('emails.layouts.staynest')

@section('content')
    <h2 style="margin: 0 0 24px; font-size: 24px; color: #1a1a1a; font-weight: 600;">
        Nueva reserva registrada
    </h2>

    <p style="margin: 0 0 24px; font-size: 16px; color: #4a4a4a; line-height: 1.6;">
        Se ha registrado una nueva reserva en tu propiedad.
    </p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; background-color: #f9f9f9; border-radius: 2px; margin-bottom: 24px;">
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666; width: 40%;">Reserva:</td>
            <td style="color: #1a1a1a;">{{ $reservation->code ?? ('#'.$reservation->id) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Cliente:</td>
            <td style="color: #1a1a1a;">{{ $reservation->user->name }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Email:</td>
            <td style="color: #1a1a1a;">{{ $reservation->user->email }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Alojamiento:</td>
            <td style="color: #1a1a1a;">{{ $reservation->property->name ?? 'Alojamiento' }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Fechas:</td>
            <td style="color: #1a1a1a;">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Huéspedes:</td>
            <td style="color: #1a1a1a;">
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
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666;">Total:</td>
            <td style="color: #4d8d94; font-weight: 700; font-size: 18px;">{{ number_format($reservation->total_price, 2, ',', '.') }} €</td>
        </tr>
        @if(!empty($reservation->notes))
        <tr style="border-bottom: 1px solid #e5e5e5;">
            <td style="font-weight: 600; color: #666666; vertical-align: top;">Notas del huésped:</td>
            <td style="color: #1a1a1a; white-space: pre-wrap;">{{ $reservation->notes }}</td>
        </tr>
        @endif
        <tr>
            <td style="font-weight: 600; color: #666666;">Estado:</td>
            <td style="color: #1a1a1a;">{{ ucfirst($reservation->status) }}</td>
        </tr>
    </table>

    <p style="margin: 0; font-size: 14px; color: #666666; line-height: 1.5;">
        Puedes gestionar esta reserva desde el panel de administración.
    </p>
@endsection
