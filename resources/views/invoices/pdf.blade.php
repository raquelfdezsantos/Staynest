<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ $invoice->number }}</title>
  <style>
    /* Embedding Inter font (pesos fijos para compatibilidad Dompdf) */
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
  @endphp
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
      @php($status = strtolower($invoice->reservation->status ?? ''))
      @if($status === 'paid')
        <div class="pill" style="border-color:#719179; color:#719179;">Pagada</div>
      @elseif($status === 'pending')
        <div class="pill" style="border-color:#E8CB74; color:#E8CB74;">Pendiente</div>
      @elseif($status === 'cancelled')
        <div class="pill" style="border-color:#CC5956; color:#CC5956;">Cancelada</div>
      @endif
    </div>
  </div>

  <div class="grid-2">
    <div class="box">
      <div class="box-title">Cliente</div>
      <div class="kv"><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
      <div class="kv"><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
      <div class="kv"><strong>Documento:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
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
      <div class="kv"><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
    </div>
  </div>

  <div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Fechas</th>
        <th class="center">Importe</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div><strong>Reserva {{ $res->code }}</strong></div>
          <div class="muted">{{ $invoice->reservation->property->name ?? 'Alojamiento' }}</div>
        </td>
        <td>{{ $invoice->reservation->check_in->format('d/m/Y') }} → {{ $invoice->reservation->check_out->format('d/m/Y') }}</td>
        <td class="right">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td class="left" colspan="2"><span class="total-label">Total</span></td>
        <td class="right"><span class="total-amount">{{ number_format($invoice->amount, 2, ',', '.') }} €</span></td>
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
