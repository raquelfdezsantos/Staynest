<x-app-layout>
    <div class="max-w-5xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header simple --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Mis reservas
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-sm);">
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
            {{-- Tabla minimalista --}}
            <div style="overflow-x: auto; margin-top: 2rem;">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Alojamiento</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Huéspedes</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $r)
                            <tr>
                                <td>{{ $r->property->name ?? '—' }}</td>
                                <td>{{ $r->check_in->format('d/m/Y') }}</td>
                                <td>{{ $r->check_out->format('d/m/Y') }}</td>
                                <td>{{ $r->guests }}</td>
                                <td>{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                                <td>
                                    @if($r->status === 'pending')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($r->status === 'paid')
                                        <span class="badge badge-success">Pagada</span>
                                    @elseif($r->status === 'cancelled')
                                        <span class="badge badge-error">Cancelada</span>
                                    @else
                                        <span class="badge badge-info">{{ ucfirst($r->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        @if($r->status === 'pending')
                                            <form method="POST" action="{{ route('stripe.checkout', $r->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn-action btn-action-primary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                                    Pagar
                                                </button>
                                            </form>
                                        @endif

                                        @if($r->status !== 'cancelled')
                                            {{-- Editar --}}
                                            <a href="{{ route('reservas.edit', $r) }}" class="btn-action btn-action-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                                Editar
                                            </a>

                                            {{-- Cancelar --}}
                                            <form method="POST" action="{{ route('reservas.cancel', $r) }}" style="display: inline;">
                                                @csrf
                                                <button class="btn-action btn-action-danger" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;"
                                                    onclick="return confirm('¿Cancelar esta reserva?')">
                                                    Cancelar
                                                </button>
                                            </form>

                                            {{-- Ver/Descargar factura si existe --}}
                                            @if($r->invoice)
                                                <a href="{{ route('invoices.show', $r->invoice->number) }}" class="btn-action btn-action-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                                    Ver factura
                                                </a>

                                                <a href="{{ route('invoices.show', $r->invoice->number) }}?download=1" class="btn-action btn-action-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                                    Descargar PDF
                                                </a>
                                            @endif

                                            {{-- Pagar diferencia si procede --}}
                                            @if($r->status === 'paid' && method_exists($r, 'balanceDue') && $r->balanceDue() > 0)
                                                <form method="POST" action="{{ route('stripe.checkout.difference', $r->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button class="btn-action btn-action-primary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem; background-color: var(--color-warning);">
                                                        Pagar diferencia ({{ number_format($r->balanceDue(), 2, ',', '.') }} €)
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginación --}}
                @if($reservations->hasPages())
                    <div style="margin-top: 2rem;">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>