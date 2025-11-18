<x-app-layout>
    <div class="max-w-5xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header simple --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Mis reservas
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">
                Gestiona tus reservas, realiza pagos y descarga tus facturas.
            </p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($reservations->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <svg style="margin: 0 auto; width: 4rem; height: 4rem; color: var(--color-text-muted); margin-bottom: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p style="color: var(--color-text-secondary); margin-bottom: 2rem; font-size: var(--text-base);">
                    No tienes reservas aún.
                </p>
                <a href="{{ route('reservar') }}" class="btn btn-primary">
                    Hacer una reserva
                </a>
            </div>
        @else
            {{-- Lista de reservas con cards --}}
            <div class="space-y-4">
                @foreach($reservations as $r)
                    <div class="reservation-card">
                        <div class="reservation-card-header">
                            <div class="reservation-property">
                                <svg class="reservation-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="reservation-property-name">Reserva #{{ $r->id }}</h3>
                            </div>
                            <div>
                                @if($r->status === 'pending')
                                    <span class="badge badge-warning">Pendiente de pago</span>
                                @elseif($r->status === 'paid')
                                    <span class="badge badge-success">Pagada</span>
                                @elseif($r->status === 'cancelled')
                                    <span class="badge badge-error">Cancelada</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst($r->status) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="reservation-card-body">
                            <div class="reservation-details">
                                <div class="reservation-detail-item">
                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <div>
                                        <p class="detail-label">Alojamiento</p>
                                        <p class="detail-value">{{ $r->property->name ?? '—' }}</p>
                                    </div>
                                </div>

                                <div class="reservation-detail-item">
                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="detail-label">Fechas</p>
                                        <p class="detail-value">{{ $r->check_in->format('d/m/Y') }} → {{ $r->check_out->format('d/m/Y') }}</p>
                                    </div>
                                </div>

                                <div class="reservation-detail-item">
                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <div>
                                        <p class="detail-label">Huéspedes</p>
                                        <p class="detail-value">
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

                                <div class="reservation-detail-item">
                                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="detail-label">Total</p>
                                        <p class="detail-value detail-value-price">{{ number_format($r->total_price, 2, ',', '.') }} €</p>
                                    </div>
                                </div>
                            </div>

                            <div class="reservation-actions">
                                @if($r->status === 'pending')
                                    <form method="POST" action="{{ route('stripe.checkout', $r->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-primary">
                                            Pagar ahora
                                        </button>
                                    </form>
                                @endif

                                @if($r->status !== 'cancelled')
                                    <a href="{{ route('reservas.edit', $r) }}" class="btn-action btn-action-secondary">
                                        Editar
                                    </a>

                                    @if($r->invoice)
                                        <a href="{{ route('invoices.show', $r->invoice->number) }}" class="btn-action btn-action-secondary">
                                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Ver factura
                                        </a>
                                    @endif

                                    @if($r->status === 'paid' && method_exists($r, 'balanceDue') && $r->balanceDue() > 0)
                                        <form method="POST" action="{{ route('stripe.checkout.difference', $r->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-action-warning">
                                                Pagar diferencia ({{ number_format($r->balanceDue(), 2, ',', '.') }} €)
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('reservas.cancel', $r) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-danger" onclick="return confirm('¿Estás seguro de que quieres cancelar esta reserva?')">
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            </div>
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