<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ $invoice->number }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
    .header { display:flex; justify-content:space-between; margin-bottom:16px; }
    .box { border:1px solid #ddd; padding:10px; border-radius:6px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { border:1px solid #eee; padding:8px; text-align:left; }
    th { background:#f7f7f7; }
    .total { text-align:right; font-weight:bold; font-size: 14px; }
  </style>
</head>
<body>
  @php
    $res = $invoice->reservation;
    $parts = [];
    $ad = (int) ($res->adults ?? 0);
    $ch = (int) ($res->children ?? 0);
    $pt = (int) ($res->pets ?? 0);
    if ($ad > 0) { $parts[] = $ad.' '.($ad === 1 ? 'adulto' : 'adultos'); }
    if ($ch > 0) { $parts[] = $ch.' '.($ch === 1 ? 'niño' : 'niños'); }
    if ($pt > 0) { $parts[] = $pt.' '.($pt === 1 ? 'mascota' : 'mascotas'); }
  @endphp
  <div class="header">
    <div>
      <h2 style="margin:0 0 6px;">Factura {{ $invoice->number }}</h2>
      <div>Emitida: {{ optional($invoice->issued_at)->format('d/m/Y') }}</div>
    </div>
    @php($p = $invoice->reservation->property)
    <div class="box">
      <strong>Alojamiento</strong><br>
      <div><strong>Nombre:</strong> {{ $p->name }}</div>
      <div><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
      <div><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
      <div><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
      <div><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
      <div><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
    </div>
  </div>

  <div class="box" style="margin-bottom: 10px;">
    <strong>Cliente</strong><br>
    <div><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
    <div><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
    <div><strong>Documento:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
    <div><strong>Correo:</strong> {{ $invoice->reservation->user->email }}</div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Fechas</th>
        <th>Importe</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Alojamiento: {{ $invoice->reservation->property->name ?? 'Alojamiento' }}</td>
        <td>{{ $invoice->reservation->check_in->format('d/m/Y') }} → {{ $invoice->reservation->check_out->format('d/m/Y') }}</td>
        <td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top:8px;">
    <strong>Huéspedes:</strong>
    @if(!empty($parts))
      {{ implode(', ', $parts) }} (total: {{ $res->guests }})
    @else
      {{ $res->guests }}
    @endif
  </p>

  <p class="total">TOTAL: {{ number_format($invoice->amount, 2, ',', '.') }} €</p>
</body>
</html>
