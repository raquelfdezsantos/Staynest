<x-app-layout>
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <style>
            .btn-nowrap { white-space: nowrap; }
            .btn-action {
                height: 36px;
                min-height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .booking-timeline {
                position: relative;
            }
            .booking-timeline::before {
                content: '';
                position: absolute;
                left: 2rem;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, rgba(var(--color-text-muted-rgb), 0.3), rgba(var(--color-text-muted-rgb), 0.1));
            }
            .booking-item {
                position: relative;
                margin-left: 4rem;
                margin-bottom: 3rem;
                padding: 2rem;
                background: rgba(var(--color-bg-secondary-rgb), 0.8);
                border: 1px solid rgba(var(--color-border-rgb), 0.1);
                border-radius: var(--radius-base);
                backdrop-filter: blur(10px);
            }
            @media (max-width: 768px) {
                .booking-timeline::before {
                    left: 1.5rem;
                }
                .booking-item {
                    margin-left: 2.5rem;
                    padding: 1.5rem;
                }
            }
            .booking-item::before {
                content: '';
                position: absolute;
                left: -2.5rem;
                top: 2rem;
                width: 1rem;
                height: 1rem;
                background: var(--color-text-muted);
                border: 3px solid var(--color-bg-secondary);
                border-radius: 50%;
            }
            @media (max-width: 768px) {
                .booking-timeline::before {
                    left: 1.5rem;
                }
                .booking-item {
                    margin-left: 2.5rem;
                    padding: 1.5rem;
                }
                .booking-item::before {
                    left: -1.5rem;
                    top: 1.5rem;
                    width: 0.75rem;
                    height: 0.75rem;
                }
            }
            .booking-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.5rem;
            }
            .booking-title {
                font-family: var(--font-serif);
                font-size: 1.25rem;
                font-weight: 600;
                color: var(--color-text-primary);
                margin: 0;
            }
            .booking-status {
                padding: 0.25rem 0.5rem;
                border-radius: 2px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: none;
                letter-spacing: 0;
                background: transparent;
                color: var(--color-text-primary);
                border: 1px solid var(--color-accent);
            }
            .booking-status.pending {
                background: transparent;
                color: var(--color-text-primary);
                border-color: var(--color-warning);
            }
            .booking-status.paid {
                background: transparent;
                color: var(--color-text-primary);
                border-color: var(--color-success);
            }
            .booking-status.cancelled {
                background: transparent;
                color: var(--color-text-primary);
                border-color: var(--color-error);
            }
            .booking-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
            .booking-detail {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .booking-detail-icon {
                width: 1.25rem;
                height: 1.25rem;
                color: var(--color-text-muted);
                flex-shrink: 0;
            }
            .booking-detail-content h4 {
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: var(--color-text-muted);
                margin: 0 0 0.25rem 0;
                font-weight: 600;
            }
            .booking-detail-content p {
                font-size: 1rem;
                color: var(--color-text-secondary);
                margin: 0;
                font-weight: 500;
            }
            .booking-price {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--color-text-primary);
                text-align: center;
                padding: 1rem;
                background: rgba(var(--color-accent-rgb), 0.2);
                border-radius: var(--radius-base);
                margin-bottom: 1.5rem;
            }
            .booking-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                justify-content: center;
            }
        </style>

        {{-- Header centrado como otras páginas públicas --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Mis reservas</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Gestiona tus reservas, realiza pagos y descarga tus facturas desde un solo lugar.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @php $currentUser = auth()->user(); @endphp
        @if($currentUser && (empty($currentUser->address) || empty($currentUser->document_id)))
            <div class="alert alert-error" style="margin-bottom:1rem;">
                Antes de realizar el pago debes completar tu dirección y NIF/CIF en <a href="{{ route('profile.edit') }}" style="text-decoration:underline;">Mi perfil</a>.
            </div>
        @endif

        @if($reservations->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <svg style="margin: 0 auto; width: 4rem; height: 4rem; color: var(--color-text-muted); margin-bottom: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p style="color: var(--color-text-secondary); margin-bottom: 2rem; font-size: var(--text-base);">
                    No tienes reservas aún.
                </p>
                @php
                    $firstProperty = \App\Models\Property::first();
                @endphp
                @if($firstProperty)
                    <a href="{{ route('properties.reservar', $firstProperty->slug) }}" class="btn btn-primary">
                        Hacer una reserva
                    </a>
                @else
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        Ver alojamientos
                    </a>
                @endif
            </div>
        @else
            {{-- Timeline de reservas --}}
            <div class="booking-timeline">
                @foreach($reservations as $r)
                    <div class="booking-item">
                        <div class="booking-header">
                            <h3 class="booking-title">{{ $r->property->name ?? 'Propiedad' }} • Reserva {{ $r->code ?? ('#'.$r->id) }}</h3>
                            @if($r->status === 'pending')
                                <span class="booking-status pending">Pendiente</span>
                            @elseif($r->status === 'paid')
                                <span class="booking-status paid">Pagada</span>
                            @elseif($r->status === 'cancelled')
                                <span class="booking-status cancelled">Cancelada</span>
                            @else
                                <span class="booking-status pending">{{ ucfirst($r->status) }}</span>
                            @endif
                        </div>

                        <div class="booking-grid">
                            <div class="booking-detail">
                                <svg class="booking-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="booking-detail-content">
                                    <h4>Fechas</h4>
                                    <p>{{ $r->check_in->format('d/m/Y') }} → {{ $r->check_out->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="booking-detail">
                                <svg class="booking-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <div class="booking-detail-content">
                                    <h4>Huéspedes</h4>
                                    <p>
                                        @php
                                            $parts = [];
                                            $ad = (int) ($r->adults ?? 0);
                                            $ch = (int) ($r->children ?? 0);
                                            $pt = (int) ($r->pets ?? 0);
                                            if ($ad > 0) { $parts[] = $ad.' '.($ad === 1 ? 'adulto' : 'adultos'); }
                                            if ($ch > 0) { $parts[] = $ch.' '.($ch === 1 ? 'niño' : 'niños'); }
                                            if ($pt > 0) { $parts[] = $pt.' '.($pt === 1 ? 'mascota' : 'mascotas'); }
                                        @endphp
                                        @if(count($parts))
                                            {{ implode(', ', $parts) }}
                                        @else
                                            {{ $r->guests }} {{ $r->guests === 1 ? 'persona' : 'personas' }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="booking-detail">
                                <svg class="booking-detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <div class="booking-detail-content">
                                    <h4>Alojamiento</h4>
                                    <p>{{ $r->property->name ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="booking-price">
                            {{ number_format($r->total_price, 2, ',', '.') }} €
                        </div>

                        <div class="booking-actions">
                            @if($r->status === 'pending')
                                @php $u = auth()->user(); @endphp
                                @if($currentUser && (empty($currentUser->address) || empty($currentUser->document_id)))
                                    <a href="{{ route('profile.edit') }}" class="btn-action btn-action-danger sn-sentence" title="Completa tus datos antes de pagar">
                                        Completar datos para pagar
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('stripe.checkout', $r->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-primary sn-sentence">
                                            Pagar ahora
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if($r->status !== 'cancelled')
                                <a href="{{ route('reservas.edit', $r) }}" class="btn-action btn-action-secondary sn-sentence">
                                    Editar
                                </a>

                                @if($r->invoice)
                                    <a href="{{ route('invoices.show', $r->invoice->number) }}" class="btn-action btn-action-secondary btn-nowrap sn-sentence">Ver factura</a>
                                @endif

                                @if($r->status === 'paid' && method_exists($r, 'balanceDue') && $r->balanceDue() > 0)
                                    <form method="POST" action="{{ route('stripe.checkout.difference', $r->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-warning sn-sentence">
                                            Pagar diferencia ({{ number_format($r->balanceDue(), 2, ',', '.') }} €)
                                        </button>
                                    </form>
                                @endif

                                <button type="button"
                                        x-data
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-reservation-cancel-{{ $r->id }}')"
                                        class="btn-action btn-action-danger sn-sentence">
                                    Cancelar
                                </button>
                                <x-modal name="confirm-reservation-cancel-{{ $r->id }}" focusable>
                                    <form method="POST" action="{{ route('reservas.cancel', $r) }}" style="padding: 2rem;">
                                        @csrf
                                        <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary);">Cancelar reserva {{ $r->code ?? ('#'.$r->id) }}</h2>
                                        @php
                                            $daysUntil = now()->diffInDays($r->check_in, false);
                                            $daysUntilInt = max(0, (int) $daysUntil);
                                            $percent = $r->cancellationRefundPercent();
                                            $paid = $r->paidAmount();
                                            $baseRefundable = min($paid, $r->total_price);
                                            $estimatedRefund = $percent > 0 ? ($baseRefundable * $percent / 100) : 0;
                                        @endphp
                                        <div style="margin-top:0.75rem; font-size: var(--text-sm); color: var(--color-text-secondary); line-height:1.4;">
                                            <p style="margin-bottom:0.75rem;">
                                                Al cancelar ahora, faltan <strong>{{ $daysUntilInt }}</strong> días para el check‑in.
                                                @if($r->status === 'paid')
                                                    @if($daysUntilInt <= 0)
                                                        El periodo de estancia ya ha comenzado, no procede reembolso.
                                                    @else
                                                        Según la política de cancelación: <br>
                                                        @if($percent > 0)
                                                            Reembolso aplicable: <strong>{{ $percent }}%</strong> sobre lo pagado (máx. el total de la reserva).
                                                        @else
                                                            No aplica reembolso ({{ $daysUntilInt }} días < 7 días antes del check‑in).
                                                        @endif
                                                    @endif
                                                @else
                                                    La reserva aún no está pagada; no se genera reembolso.
                                                @endif
                                            </p>
                                            @if($r->status === 'paid')
                                                <p style="margin-bottom:0.5rem;">
                                                    Has pagado: <strong>{{ number_format($paid, 2, ',', '.') }} €</strong> de un total de {{ number_format($r->total_price, 2, ',', '.') }} €.
                                                </p>
                                                @if($percent > 0 && $daysUntilInt >= 0)
                                                    <p style="margin-bottom:0.5rem;">
                                                        Estimación de reembolso: <strong>{{ number_format($estimatedRefund, 2, ',', '.') }} €</strong> ({{ $percent }}% de {{ number_format($baseRefundable, 2, ',', '.') }} €).
                                                    </p>
                                                @endif
                                                @if($percent === 0 || $daysUntilInt <= 0)
                                                    <p style="margin-bottom:0.5rem; color: var(--color-text-muted);">No se realizará devolución.</p>
                                                @endif
                                            @endif
                                            <p style="margin-top:0.75rem;">
                                                ¿Confirmas la cancelación? Esta acción es irreversible y liberará las noches.
                                            </p>
                                        </div>
                                        <div style="margin-top:1.5rem; display:flex; justify-content:flex-end; gap:0.75rem;">
                                            <button type="button"
                                                    x-on:click="$dispatch('close-modal', 'confirm-reservation-cancel-{{ $r->id }}')"
                                                    class="btn-action btn-action-secondary sn-sentence">Volver</button>
                                            <button type="submit"
                                                    style="padding: 0.5rem 1.25rem; font-size: var(--text-sm); font-weight: 600; color: white; background-color: var(--color-error); border: none; border-radius: 2px; cursor: pointer; transition: background-color var(--transition-fast);"
                                                    onmouseover="this.style.backgroundColor='#d87876'"
                                                    onmouseout="this.style.backgroundColor='var(--color-error)'">
                                                Confirmar cancelación
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($reservations->hasPages())
                <div style="margin-top: 2rem;">
                    {{ $reservations->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>