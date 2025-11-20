@extends('layouts.app')
@section('title','Factura ' . $invoice->number)
@section('content')
    <div class="invoice-page max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-serif mb-1">Factura {{ $invoice->number }}</h1>
                <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Emitida: {{ optional($invoice->issued_at)->format('d/m/Y H:i') }}</p>
            </div>
            @php($status = strtolower($invoice->reservation->status ?? ''))
            @if($status === 'paid')
                <span class="badge badge-success">Pagada</span>
            @elseif($status === 'pending')
                <span class="badge badge-warning">Pendiente</span>
            @elseif($status === 'cancelled')
                <span class="badge badge-error">Cancelada</span>
            @else
                <span class="badge badge-info">{{ ucfirst($invoice->reservation->status ?? '—') }}</span>
            @endif
        </header>

        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="md:col-span-2 space-y-6">
                <section class="invoice-card p-5" style="border-radius:var(--radius-base); border:1px solid var(--color-border-light); background: var(--color-bg-primary);">
                    <h2 style="font-size: var(--text-base); font-weight:600; color: var(--color-text-primary); margin:0 0 0.75rem; text-transform: uppercase; letter-spacing:0.05em;">Datos</h2>
                    <div class="grid sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-neutral-400 mb-2" style="font-size: var(--text-base); color: var(--color-text-primary);">Cliente</p>
                            <div class="invoice-legal text-neutral-200" style="font-size: 0.875rem; color:#999999;">
                                <div style="margin-bottom:0.6rem;"><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>NIF/CIF/PAS/Otro:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Correo:</strong> {{ $invoice->reservation->user->email }}</div>
                            </div>
                        </div>
                        <div>
                            <p class="text-neutral-400 mb-2" style="font-size: var(--text-base); color: var(--color-text-primary);">Alojamiento</p>
                            @php($p = $invoice->reservation->property)
                            <div class="invoice-legal text-neutral-200" style="font-size: 0.875rem; color:#999999;">
                                <div style="margin-bottom:0.6rem;"><strong>Nombre:</strong> {{ $p->name }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;"><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
                                <div style="margin-bottom:0.6rem;">Check-in: {{ $invoice->reservation->check_in->format('d/m/Y') }}</div>
                                <div style="margin-bottom:0.6rem;">Check-out: {{ $invoice->reservation->check_out->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-card p-5" style="border-radius:var(--radius-base); border:1px solid var(--color-border-light); background: var(--color-bg-primary);">
                    @php($res = $invoice->reservation)
                    @php($nights = $res->check_in && $res->check_out ? $res->check_in->diffInDays($res->check_out) : 0)
                    @php($parts = [])
                    @php($ad = (int)($res->adults ?? 0))
                    @php($ch = (int)($res->children ?? 0))
                    @php($pt = (int)($res->pets ?? 0))
                    @if($ad>0) @php($parts[] = $ad.' '.($ad===1?'adulto':'adultos')) @endif
                    @if($ch>0) @php($parts[] = $ch.' '.($ch===1?'niño':'niños')) @endif
                    @if($pt>0) @php($parts[] = $pt.' '.($pt===1?'mascota':'mascotas')) @endif
                    <h2 style="font-size: var(--text-base); font-weight:600; color: var(--color-text-primary); margin:0 0 0.75rem; text-transform: uppercase; letter-spacing:0.05em;">Resumen</h2>
                    <div class="overflow-hidden text-sm">
                         <table class="w-full text-sm">
                            <thead>
                                <tr class="text-neutral-300 text-center" style="color:#999999;">
                                    <th class="py-2 font-medium" style="font-size: var(--text-base); text-align:center;">Concepto</th>
                                    <th class="py-2 font-medium" style="font-size: var(--text-base); text-align:center;">Noches</th>
                                    <th class="py-2 font-medium" style="font-size: var(--text-base); text-align:center;">Huéspedes</th>
                                    <th class="py-2 font-medium" style="font-size: var(--text-base); text-align:center;">Importe</th>
                                </tr>
                            </thead>
                            <tbody class="border-t border-neutral-700">
                                <tr>
                                     <td class="py-2 pr-4">
                                         <div style="color:#fff;">Reserva {{ $res->code }}</div>
                                         <div style="color:#fff;">{{ $res->property->name }}</div>
                                     </td>
                                     <td class="py-2" style="color:#999999;">{{ $nights }}</td>
                                     <td class="py-2" style="color:#999999;">@if(count($parts)) {{ implode(', ',$parts) }} (total: {{ $res->guests }}) @else {{ $res->guests }} @endif</td>
                                     <td class="py-2 text-center font-semibold" style="color:#999999;">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t border-neutral-700" style="font-size:1rem;">
                                <tr>
                                    <td colspan="3" class="py-2 font-medium" style="text-align:left; color:#999999;">Total</td>
                                    <td class="py-2 text-center font-bold" style="color:#fff !important;">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                @if($res->notes)
                <section class="invoice-card p-5" style="border-radius:var(--radius-base); border:1px solid var(--color-border-light); background: var(--color-bg-primary);">
                    <h2 style="font-size: var(--text-base); font-weight:600; color: var(--color-text-primary); margin:0 0 0.75rem; text-transform: uppercase; letter-spacing:0.05em;">Notas de la reserva</h2>
                    <p class="text-neutral-300 whitespace-pre-line text-sm">{{ $res->notes }}</p>
                </section>
                @endif
            </div>

                        <aside class="space-y-6">
                                <div class="p-5 invoice-actions" style="border-radius:var(--radius-base); background: var(--color-bg-primary); border:1px solid var(--color-border-light);">
                    <h3 style="font-size: var(--text-base); font-weight:600; color:#d1d1d1; margin:0 0 0.75rem; text-transform: uppercase; letter-spacing:0.05em;">Acciones</h3>
                    <div class="flex flex-col gap-2">
                                                @php($backUrl = request()->is('admin/*') ? route('admin.invoices.index') : route('invoices.index'))
                                                <a href="{{ $backUrl }}" class="btn-action btn-action-secondary">← Volver</a>
                                                <a href="{{ route('invoices.show', $invoice->number) }}?download=1" class="btn-action btn-action-primary">Descargar <span style="text-transform:uppercase">PDF</span></a>
                    </div>
                </div>
                <div class="text-xs text-neutral-500 leading-relaxed" style="color:#666666;">
                    Esta factura refleja el importe total de la reserva. El desglose de pagos y devoluciones, si hubiera, se conserva en el historial de la reserva.
                </div>
            </aside>
        </div>
    </div>
        <style>
            /* Estilos específicos para la factura */
            html[data-theme="dark"] .invoice-actions { color: #d1d1d1; }
            /* Modo claro: mapear fondos/bordes como en /reservar */
            html[data-theme="light"] .invoice-page .bg-neutral-800 { background-color: var(--color-bg-secondary) !important; }
            html[data-theme="light"] .invoice-page .border-neutral-700 { border-color: var(--color-border-light) !important; }
            html[data-theme="light"] .invoice-page .text-neutral-300 { color: var(--color-text-secondary) !important; }
            html[data-theme="light"] .invoice-page .text-neutral-400 { color: var(--color-text-muted) !important; }
            html[data-theme="light"] .invoice-page .text-neutral-200 { color: var(--color-text-primary) !important; }
            /* Igualar estilo de botones al de Mis reservas */
            .invoice-actions .btn-action {
                text-transform: none;
                letter-spacing: 0;
                font-weight: 600;
                line-height: 1.25rem; /* text-sm */
                font-size: var(--text-sm);
                padding: 0.5rem 1.25rem; /* px-5 py-2 */
                border-radius: 2px;
            }
            .invoice-actions .btn-action-primary:hover {
                transform: none;
                box-shadow: none;
                color: #fff !important; /* Evitar que el a:hover global cambie el color */
            }
            .invoice-actions .btn-action-primary,
            .invoice-actions .btn-action-primary:focus {
                color: #fff !important;
            }
            /* Usar estilos globales para btn-action-secondary (borde teal, hover sin borde) */
        </style>
@endsection
