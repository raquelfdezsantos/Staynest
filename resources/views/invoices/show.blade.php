@extends('layouts.app')
@section('title','Factura ' . $invoice->number)
@section('content')
    <div class="invoice-page max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-serif mb-1">Factura {{ $invoice->number }}</h1>
                <p class="text-neutral-400 text-sm">Emitida: {{ optional($invoice->issued_at)->format('d/m/Y H:i') }}</p>
            </div>
            @php($status = strtolower($invoice->reservation->status ?? ''))
            @if($status === 'paid')
                <span class="text-xs px-3 py-1" style="border-radius:2px; border:1px solid var(--color-success); color: var(--color-success); background: rgba(16,185,129,0.08);">Estado: Pagada</span>
            @elseif($status === 'pending')
                <span class="text-xs px-3 py-1" style="border-radius:2px; border:1px solid var(--color-warning, #eab308); color: var(--color-warning, #eab308); background: rgba(234,179,8,0.08);">Estado: Pendiente</span>
            @elseif($status === 'cancelled')
                <span class="text-xs px-3 py-1" style="border-radius:2px; border:1px solid var(--color-error); color: var(--color-error); background: rgba(204, 89, 86, 0.10);">Estado: Cancelada</span>
            @else
                <span class="text-xs px-3 py-1" style="border-radius:2px; border:1px solid var(--color-border-light); color: var(--color-text-secondary);">Estado: {{ ucfirst($invoice->reservation->status ?? '—') }}</span>
            @endif
        </header>

        <div class="grid md:grid-cols-3 gap-6 mb-10">
            <div class="md:col-span-2 space-y-6">
                <section class="invoice-card bg-neutral-800 border border-neutral-700 p-5" style="border-radius:var(--radius-base);">
                    <h2 class="text-sm uppercase tracking-wide text-neutral-400 mb-3">Datos</h2>
                    <div class="grid sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-neutral-400 mb-1">Cliente</p>
                            <div class="invoice-legal text-neutral-200">
                                <div><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
                                <div><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
                                <div><strong>Documento:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
                                <div><strong>Correo:</strong> {{ $invoice->reservation->user->email }}</div>
                            </div>
                        </div>
                        <div>
                            <p class="text-neutral-400 mb-1">Alojamiento</p>
                            @php($p = $invoice->reservation->property)
                            <div class="invoice-legal text-neutral-200">
                                <div><strong>Nombre:</strong> {{ $p->name }}</div>
                                <div><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
                                <div><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
                                <div><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
                                <div><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
                                <div><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
                                <div class="text-neutral-400 mt-2">Check-in: {{ $invoice->reservation->check_in->format('d/m/Y') }}</div>
                                <div class="text-neutral-400">Check-out: {{ $invoice->reservation->check_out->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-card bg-neutral-800 border border-neutral-700 p-5" style="border-radius:var(--radius-base);">
                    @php($res = $invoice->reservation)
                    @php($nights = $res->check_in && $res->check_out ? $res->check_in->diffInDays($res->check_out) : 0)
                    @php($parts = [])
                    @php($ad = (int)($res->adults ?? 0))
                    @php($ch = (int)($res->children ?? 0))
                    @php($pt = (int)($res->pets ?? 0))
                    @if($ad>0) @php($parts[] = $ad.' '.($ad===1?'adulto':'adultos')) @endif
                    @if($ch>0) @php($parts[] = $ch.' '.($ch===1?'niño':'niños')) @endif
                    @if($pt>0) @php($parts[] = $pt.' '.($pt===1?'mascota':'mascotas')) @endif
                    <h2 class="text-sm uppercase tracking-wide text-neutral-400 mb-3">Resumen</h2>
                    <div class="overflow-hidden text-sm">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-neutral-300 text-left">
                                    <th class="py-2 font-medium">Concepto</th>
                                    <th class="py-2 font-medium">Noches</th>
                                    <th class="py-2 font-medium">Huéspedes</th>
                                    <th class="py-2 font-medium text-right">Importe</th>
                                </tr>
                            </thead>
                            <tbody class="border-t border-neutral-700">
                                <tr>
                                    <td class="py-2 pr-4">
                                        <div>Reserva {{ $res->code }}</div>
                                        <div class="text-neutral-400">{{ $res->property->name }}</div>
                                    </td>
                                    <td class="py-2">{{ $nights }}</td>
                                    <td class="py-2">@if(count($parts)) {{ implode(', ',$parts) }} (total: {{ $res->guests }}) @else {{ $res->guests }} @endif</td>
                                    <td class="py-2 text-right font-semibold">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t border-neutral-700">
                                <tr>
                                    <td colspan="3" class="py-2 font-medium">Total</td>
                                    <td class="py-2 text-right font-bold">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                @if($res->notes)
                <section class="invoice-card bg-neutral-800 border border-neutral-700 p-5" style="border-radius:var(--radius-base);">
                    <h2 class="text-sm uppercase tracking-wide text-neutral-400 mb-3">Notas de la reserva</h2>
                    <p class="text-neutral-300 whitespace-pre-line text-sm">{{ $res->notes }}</p>
                </section>
                @endif
            </div>

                        <aside class="space-y-6">
                                <div class="bg-neutral-800 border border-neutral-700 p-5 invoice-actions" style="border-radius:var(--radius-base);">
                    <h3 class="text-sm uppercase tracking-wide text-neutral-400 mb-3">Acciones</h3>
                    <div class="flex flex-col gap-2">
                                                @php($backUrl = request()->is('admin/*') ? route('admin.invoices.index') : route('invoices.index'))
                                                <a href="{{ $backUrl }}" class="btn-action btn-action-secondary">← Volver</a>
                                                <a href="{{ route('invoices.show', $invoice->number) }}?download=1" class="btn-action btn-action-primary">Descargar PDF</a>
                    </div>
                </div>
                <div class="text-xs text-neutral-500 leading-relaxed">
                    Esta factura refleja el importe total de la reserva. El desglose de pagos y devoluciones, si hubiera, se conserva en el historial de la reserva.
                </div>
            </aside>
        </div>
    </div>
        <style>
            /* Forzar color de texto suave SOLO en modo oscuro (igual que /entorno) */
            html[data-theme="dark"] .invoice-card { color: #d1d1d1; }
            html[data-theme="dark"] .invoice-card h2,
            html[data-theme="dark"] .invoice-card p,
            html[data-theme="dark"] .invoice-card div,
            html[data-theme="dark"] .invoice-card span,
            html[data-theme="dark"] .invoice-card th,
            html[data-theme="dark"] .invoice-card td,
            html[data-theme="dark"] .invoice-card strong { color: #d1d1d1; }
            html[data-theme="dark"] .invoice-actions { color: #d1d1d1; }
            .invoice-legal { color: var(--color-text-secondary); }
            .invoice-legal strong { color: var(--color-text-secondary); font-weight: 600; }
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
            .invoice-actions .btn-action-secondary {
                background: transparent;
                border: none;
                color: var(--color-text-primary);
            }
            .invoice-actions .btn-action-secondary:hover {
                background-color: rgba(77, 141, 148, 0.10);
                color: var(--color-accent);
            }
        </style>
@endsection
