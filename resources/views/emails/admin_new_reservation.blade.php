<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Nueva reserva</title></head>
<body style="font-family: Arial, sans-serif; color:#222; line-height:1.5;">
  <h2 style="margin:0 0 12px;">Nueva reserva registrada</h2>
  <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
    <tr><td><strong>ID:</strong></td><td>#{{ $reservation->id }}</td></tr>
    <tr><td><strong>Cliente:</strong></td><td>{{ $reservation->user->name }} ({{ $reservation->user->email }})</td></tr>
    <tr><td><strong>Alojamiento:</strong></td><td>{{ $reservation->property->name ?? 'Alojamiento' }}</td></tr>
    <tr><td><strong>Fechas:</strong></td><td>{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</td></tr>
    <tr>
      <td><strong>Huéspedes:</strong></td>
      <td>
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
    <tr><td><strong>Total:</strong></td><td>{{ number_format($reservation->total_price, 2, ',', '.') }} €</td></tr>
    <tr><td><strong>Estado:</strong></td><td>{{ ucfirst($reservation->status) }}</td></tr>
  </table>
</body>
</html>
