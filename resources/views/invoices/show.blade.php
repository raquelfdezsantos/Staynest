@extends('layouts.app')
@section('title','Factura ' . $invoice->number)
@section('content')
    <div class="invoice-page max-w-5xl mx-auto px-4 py-10">
        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Factura {{ $invoice->number }}</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Emitida: {{ optional($invoice->issued_at)->format('d/m/Y H:i') }}</p>
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
                <section class="invoice-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Datos</h2>
                    <div class="grid sm:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="mb-3" style="font-size: var(--text-base); color: var(--color-text-primary); font-weight: 500;">Cliente</p>
                            <div class="text-sm" style="color: var(--color-text-secondary);">
                                <div class="mb-2"><strong>Nombre:</strong> {{ $invoice->reservation->user->name }}</div>
                                <div class="mb-2"><strong>Dirección:</strong> {{ $invoice->reservation->user->address ?? '—' }}</div>
                                <div class="mb-2"><strong>NIF/CIF/PAS/Otro:</strong> {{ $invoice->reservation->user->document_id ?? '—' }}</div>
                                <div class="mb-2"><strong>Correo:</strong> {{ $invoice->reservation->user->email }}</div>
                            </div>
                        </div>
                        <div>
                            <p class="mb-3" style="font-size: var(--text-base); color: var(--color-text-primary); font-weight: 500;">Alojamiento</p>
                            @php($p = $invoice->reservation->property)
                            <div class="text-sm" style="color: var(--color-text-secondary);">
                                <div class="mb-2"><strong>Nombre:</strong> {{ $p->name }}</div>
                                <div class="mb-2"><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
                                <div class="mb-2"><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
                                <div class="mb-2"><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
                                <div class="mb-2"><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
                                <div class="mb-2"><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
                                <div class="mb-2">Check-in: {{ $invoice->reservation->check_in->format('d/m/Y') }}</div>
                                <div class="mb-2">Check-out: {{ $invoice->reservation->check_out->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    @php($res = $invoice->reservation)
                    @php($nights = $res->check_in && $res->check_out ? $res->check_in->diffInDays($res->check_out) : 0)
                    @php($parts = [])
                    @php($ad = (int)($res->adults ?? 0))
                    @php($ch = (int)($res->children ?? 0))
                    @php($pt = (int)($res->pets ?? 0))
                    @if($ad>0) @php($parts[] = $ad.' '.($ad===1?'adulto':'adultos')) @endif
                    @if($ch>0) @php($parts[] = $ch.' '.($ch===1?'niño':'niños')) @endif
                    @if($pt>0) @php($parts[] = $pt.' '.($pt===1?'mascota':'mascotas')) @endif
                    <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Resumen</h2>
                    <div class="overflow-hidden text-sm">
                         <table class="w-full text-sm" style="border-collapse: collapse;">
                            <thead>
                                <tr class="text-center">
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:left; color: var(--color-text-secondary);">Concepto</th>
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:center; color: var(--color-text-secondary);">Noches</th>
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:center; color: var(--color-text-secondary);">Huéspedes</th>
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:right; color: var(--color-text-secondary);">Importe</th>
                                </tr>
                            </thead>
                            <tbody class="border-t" style="border-color: rgba(var(--color-border-rgb), 0.1);">
                                <tr style="transition: all 0.3s ease;" onmouseover="this.style.backgroundColor = 'rgba(var(--color-bg-card-rgb), 0.9)';" onmouseout="this.style.backgroundColor = 'transparent';">
                                     <td class="py-3 pr-4">
                                         <div style="color: var(--color-text-primary); font-weight: 500;">Reserva {{ $res->code }}</div>
                                         <div style="color: var(--color-text-primary); font-weight: 500;">{{ $res->property->name }}</div>
                                     </td>
                                     <td class="py-3 text-center" style="color: var(--color-text-secondary);">{{ $nights }}</td>
                                     <td class="py-3 text-center" style="color: var(--color-text-secondary);">@if(count($parts)) {{ implode(', ',$parts) }} (total: {{ $res->guests }}) @else {{ $res->guests }} @endif</td>
                                     <td class="py-3 text-right font-semibold" style="color: var(--color-text-secondary);">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t" style="border-color: rgba(var(--color-border-rgb), 0.1); font-size:1rem;">
                                <tr>
                                    <td colspan="3" class="py-3 font-medium text-left" style="color: var(--color-text-secondary);">Total</td>
                                    <td class="py-3 text-right font-bold" style="color: var(--color-text-primary);">{{ number_format($invoice->amount,2,',','.') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                @if($res->notes)
                <section class="invoice-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Notas de la reserva</h2>
                    <p style="color: var(--color-text-secondary); font-size: var(--text-base); whitespace-pre-line;">{{ $res->notes }}</p>
                </section>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    <h3 style="font-size: var(--text-lg); font-weight:600; margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em; color: var(--color-text-primary);">Acciones</h3>
                    <div class="flex flex-col gap-3 invoice-actions">
                        @php($backUrl = Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('invoices.index'))
                        <a href="{{ $backUrl }}" class="btn-action btn-action-secondary">Volver</a>
                        <a href="{{ route('invoices.show', $invoice->number) }}?download=1" class="btn-action btn-action-primary">Descargar PDF</a>
                    </div>
                </div>
                <p class="text-sm leading-relaxed mt-4" style="color: var(--color-text-muted);">
                    Esta factura refleja el importe total de la reserva. El desglose de pagos y devoluciones, si hubiera, se conserva en el historial de la reserva.
                </p>
            </aside>
        </div>

        <style>
            .invoice-actions .btn-action-primary,
            .invoice-actions .btn-action-primary:focus {
                color: #fff !important;
            }
            /* Hover Volver: mismo efecto que header público (.nav-link) */
            .invoice-actions .btn-action-secondary:hover {
                color: var(--color-accent) !important;
                background-color: rgba(77, 141, 148, 0.10) !important;
                border-color: transparent !important;
            }
            .invoice-actions .btn-action {
                text-transform: none !important;
                height: 36px;
                min-height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 1rem;
            }
            .invoice-actions .btn-action::first-letter {
                text-transform: uppercase !important;
            }
        </style>
    </div>
@endsection
