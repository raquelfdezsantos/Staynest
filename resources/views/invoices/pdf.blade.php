<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ $invoice->number }}</title>
  <style>
    /* Inter font (pesos fijos para compatibilidad Dompdf) */
    @font-face {
      font-family: 'Inter';
      font-style: normal;
      font-weight: 400;
      src: url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Inter';
      font-style: italic;
      font-weight: 400;
      src: url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Inter';
      font-style: normal;
      font-weight: 600;
      src: url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Inter';
      font-style: italic;
      font-weight: 600;
      src: url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Inter';
      font-style: normal;
      font-weight: 700;
      src: url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'Inter';
      font-style: italic;
      font-weight: 700;
      src: url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.woff2') }}') format('woff2'),
           url('{{ public_path('fonts/inter/Inter-Italic-VariableFont_opsz,wght.ttf') }}') format('truetype');
    }
    /* Base (alineado a staynest.css - tema claro) */
    :root { --accent: #4D8D94; --text: #222222; --muted: #555555; --border: #4A4A4A; --bw: 0.5pt; }
    body { font-family: 'Inter', Arial, sans-serif; font-size: 12px; color: var(--text); margin: 28px; }
    h1, h2, h3 { margin: 0 0 6px; font-weight: 600; color: #181818; font-family: 'Inter', Arial, sans-serif; }
    .brand { display:flex; align-items:center; gap:12px; }
    .brand img { height: 52px; }
    .brand-name { font-size: 18px; font-weight: 700; letter-spacing: 0.3px; color:#181818; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; }
    .meta { text-align:right; font-size: 12px; color: var(--muted); }
    .pill { display:inline-block; border:1px solid var(--accent); color: var(--accent); padding: 2px 8px; border-radius: 2px; font-size: 10px; }

    /* Boxes */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .box { border:var(--bw) solid var(--border); padding:10px 12px; border-radius:2px; }
    .box-title { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 6px; }
    .kv { margin: 2px 0; }
    .kv strong { color:#181818; }

    /* Tabla */
    .table-wrap { border:var(--bw) solid var(--border); border-radius:2px; overflow:hidden; margin-top:12px; }
    table { width:100%; border-collapse:collapse; font-size: 12px; border:var(--bw) solid var(--border); }
    table, thead th, tbody td, tfoot td, strong, b { font-family: 'Inter', Arial, sans-serif; }
    thead th { background:#E6E6E6; color:#181818; border:var(--bw) solid var(--border); padding:6px 8px; text-align:center; font-weight:600; }
    tbody td { border:var(--bw) solid var(--border); padding:6px 8px; }
    tfoot td { border:var(--bw) solid var(--border); padding:6px 8px; }
    .right { text-align:right; }
    .muted { color: var(--muted); }
    .total-row td { background:#F3F3F3; color:#181818; font-weight:600; }
    .total-row td:first-child { text-align:left; border-right:none; }
    .total-row td:last-child { text-align:right; border-left:none; }
    /* Refuerzo fuente Inter en tfoot */
    tfoot td, tfoot span { font-family:'Inter', Arial, sans-serif !important; }
    .total-label, .total-amount { font-weight:600; letter-spacing:.02em; }
    tfoot strong { font-family:'Inter', Arial, sans-serif !important; }

    /* Footer */
    .note { margin-top: 10px; color: var(--muted); font-size: 11px; }
  </style>
</head>
<body>
  @php
    $logoPath = public_path('images/logos/logo-light.png');
    $hasLogo = is_file($logoPath);
    $p = $invoice->reservation->property;
    $res = $invoice->reservation;
    
    // Para facturas rectificativas, mostrar siempre las fechas ORIGINALES en la línea principal
    $isRectificative = ($invoice->amount < 0 || (is_array($invoice->details ?? null) && in_array($invoice->details['context'] ?? '', ['decrease_update', 'increase_update'])));
    
    if ($isRectificative && is_array($invoice->details ?? null)) {
      // Fechas originales de la reserva
      $displayCheckIn = !empty($invoice->details['previous_check_in']) 
        ? \Carbon\Carbon::parse($invoice->details['previous_check_in']) 
        : $res->check_in;
      $displayCheckOut = !empty($invoice->details['previous_check_out']) 
        ? \Carbon\Carbon::parse($invoice->details['previous_check_out']) 
        : $res->check_out;
      $displayAmount = $invoice->details['previous_total'] ?? abs($invoice->amount);
      
      // Calcular cambios
      $newCheckIn = !empty($invoice->details['new_check_in']) 
        ? \Carbon\Carbon::parse($invoice->details['new_check_in']) 
        : $res->check_in;
      $newCheckOut = !empty($invoice->details['new_check_out']) 
        ? \Carbon\Carbon::parse($invoice->details['new_check_out']) 
        : $res->check_out;
      $datesChanged = $displayCheckIn->format('Y-m-d') !== $newCheckIn->format('Y-m-d') 
                   || $displayCheckOut->format('Y-m-d') !== $newCheckOut->format('Y-m-d');
    } else {
      // Factura normal: usar fechas actuales de la reserva
      $displayCheckIn = $res->check_in;
      $displayCheckOut = $res->check_out;
      $displayAmount = $invoice->amount;
      $datesChanged = false;
      $newCheckIn = $res->check_in;
      $newCheckOut = $res->check_out;
    }
    
    // Calcular huéspedes ORIGINALES (primera fila) - siempre los datos previos para rectificativas
    if ($isRectificative && !empty($invoice->details['previous_adults'])) {
        $displayAdults = (int)$invoice->details['previous_adults'];
        $displayChildren = (int)($invoice->details['previous_children'] ?? 0);
        $displayPets = (int)($invoice->details['previous_pets'] ?? 0);
    } elseif (!empty($invoice->details['adults'])) {
        $displayAdults = (int)$invoice->details['adults'];
        $displayChildren = (int)($invoice->details['children'] ?? 0);
        $displayPets = (int)($invoice->details['pets'] ?? 0);
    } else {
        $displayAdults = (int)($res->adults ?? 0);
        $displayChildren = (int)($res->children ?? 0);
        $displayPets = (int)($res->pets ?? 0);
    }
    
    $parts = [];
    if ($displayAdults > 0) { $parts[] = $displayAdults.' '.($displayAdults === 1 ? 'adulto' : 'adultos'); }
    if ($displayChildren > 0) { $parts[] = $displayChildren.' '.($displayChildren === 1 ? 'niño' : 'niños'); }
    if ($displayPets > 0) { $parts[] = $displayPets.' '.($displayPets === 1 ? 'mascota' : 'mascotas'); }
    
    // Para la fila de cambios - NUEVOS valores en rectificativas
    $newAdults = (int)($invoice->details['new_adults'] ?? 0);
    $newChildren = (int)($invoice->details['new_children'] ?? 0);
    $newPets = (int)($invoice->details['new_pets'] ?? 0);
    $newParts = [];
    if ($newAdults > 0) { $newParts[] = $newAdults.' '.($newAdults === 1 ? 'adulto' : 'adultos'); }
    if ($newChildren > 0) { $newParts[] = $newChildren.' '.($newChildren === 1 ? 'niño' : 'niños'); }
    if ($newPets > 0) { $newParts[] = $newPets.' '.($newPets === 1 ? 'mascota' : 'mascotas'); }
    $guestsChanged = $displayAdults !== $newAdults || $displayChildren !== $newChildren || $displayPets !== $newPets;
    
    $status = strtolower($invoice->reservation->status ?? '');
  @endphp
  <div class="header">
    <div class="brand">
      @if($hasLogo)
        <img src="{{ $logoPath }}" alt="Staynest">
      @else
        <div class="brand-name">{{ config('app.name','Staynest') }}</div>
      @endif
    </div>
    <div class="meta">
      <div style="font-size:14px; font-weight:700; color:#181818;">Factura {{ $invoice->number }}</div>
      <div>Emitida: {{ optional($invoice->issued_at)->format('d/m/Y') }}</div>
      @if($status === 'paid')
        <div class="pill" style="border-color:#8FBC8F; color:#000000;">Pagada</div>
      @elseif($status === 'pending')
        <div class="pill" style="border-color:#FFD54F; color:#000000;">Pendiente</div>
      @elseif($status === 'cancelled')
        <div class="pill" style="border-color:#E57373; color:#000000;">Cancelada</div>
      @else
        <div class="pill" style="border-color:#4D8D94; color:#000000;">{{ ucfirst($status) }}</div>
      @endif
    </div>
  </div>

  <div class="grid-2">
    <div class="box">
      <div class="box-title">Cliente</div>
      <div class="kv"><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
      <div class="kv"><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
      <div class="kv"><strong>NIF/CIF/PAS/Otro:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
      <div class="kv"><strong>Correo:</strong> {{ $invoice->reservation->user->email }}</div>
    </div>
    <br>
    <div class="box">
      <div class="box-title">Alojamiento</div>
      <div class="kv"><strong>Nombre:</strong> {{ $p->name }}</div>
      <div class="kv"><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
      <div class="kv"><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
      <div class="kv"><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
      <div class="kv"><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
      <div class="kv"><strong>NIF/CIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
    </div>
  </div>

  <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Fechas</th>
        <th>Huéspedes</th>
        <th class="center">Importe</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div><strong>Reserva {{ $res->code }}</strong></div>
          <div class="muted">{{ $invoice->reservation->property->name ?? 'Alojamiento' }}</div>
        </td>
        <td>{{ $displayCheckIn->format('d/m/Y') }} → {{ $displayCheckOut->format('d/m/Y') }}</td>
        <td class="center">
          @if(!empty($parts))
            {{ implode(', ', $parts) }}
          @else
            {{ $invoice->details['guests'] ?? $res->guests }}
          @endif
        </td>
        <td class="right">{{ number_format($displayAmount, 2, ',', '.') }} €</td>
      </tr>
      @if($isRectificative && is_array($invoice->details))
      <tr>
        <td><span class="muted">Cambios aplicados</span></td>
        <td>
          @if($datesChanged)
            {{ $newCheckIn->format('d/m/Y') }} → {{ $newCheckOut->format('d/m/Y') }}
          @else
            —
          @endif
        </td>
        <td class="center">
          @if($guestsChanged)
            @if(!empty($newParts))
              {{ implode(', ', $newParts) }}
            @else
              {{ $invoice->details['new_guests'] ?? '—' }}
            @endif
          @else
            —
          @endif
        </td>
        <td class="right" style="color:{{ $invoice->amount < 0 ? '#dc3545' : '#28a745' }}">
          {{ $invoice->amount < 0 ? '-' : '+' }}{{ number_format(abs($invoice->details['difference'] ?? $invoice->amount), 2, ',', '.') }} €
        </td>
      </tr>
      @endif
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td class="left" colspan="3"><span class="total-label">Total</span></td>
        <td class="right">
          <span class="total-amount">
            @if($isRectificative && is_array($invoice->details))
              {{ number_format($invoice->details['new_total'] ?? $invoice->reservation->total_price, 2, ',', '.') }} €
            @else
              {{ number_format($invoice->amount, 2, ',', '.') }} €
            @endif
          </span>
        </td>
      </tr>
    </tfoot>
  </table>
  </div>

  

  <p style="margin-top:8px;">
    <strong>Huéspedes:</strong>
    @if(!empty($parts))
      {{ implode(', ', $parts) }} (total: {{ $res->guests }})
    @else
      {{ $res->guests }}
    @endif
  </p>

  <p class="note">Este documento refleja el importe total de la reserva. Los detalles de pagos y devoluciones constan en el historial de la reserva.</p>
</body>
</html>
