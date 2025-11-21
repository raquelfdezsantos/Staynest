<x-app-layout>
    <div class="sn-reservar max-w-4xl mx-auto px-4 py-10">
        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Reserva #{{ $reservation->code ?? $reservation->id }}</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Detalles completos de la reserva.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        {{-- Sección de detalles de la reserva --}}
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            {{-- Código y Estado --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Información General</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Código de Reserva</p>
                        <p style="font-size: var(--text-base); color: var(--color-text-primary); font-family: monospace; font-weight: 600;">#{{ $reservation->code ?? $reservation->id }}</p>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Estado</p>
                        @if($reservation->status === 'pending')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($reservation->status === 'paid')
                            <span class="badge badge-success">Pagada</span>
                        @elseif($reservation->status === 'cancelled')
                            <span class="badge badge-error">Cancelada</span>
                        @else
                            <span class="badge badge-info">{{ ucfirst($reservation->status) }}</span>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Fecha de Creación</p>
                        <p style="font-size: var(--text-base); color: var(--color-text-primary);">{{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Propiedad --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Propiedad</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3rem; height: 3rem; border-radius: 50%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: var(--color-text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">{{ $reservation->property->name ?? 'Propiedad' }}</p>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">{{ $reservation->property->address ?? '' }}, {{ $reservation->property->city ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Fechas</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Check-in</p>
                        <p style="font-size: var(--text-lg); color: var(--color-text-primary); font-weight: 600;">{{ $reservation->check_in->format('d/m/Y') }}</p>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">{{ $reservation->check_in->format('l, j \d\e F') }}</p>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Check-out</p>
                        <p style="font-size: var(--text-lg); color: var(--color-text-primary); font-weight: 600;">{{ $reservation->check_out->format('d/m/Y') }}</p>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">{{ $reservation->check_out->format('l, j \d\e F') }}</p>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Noches</p>
                        <p style="font-size: var(--text-lg); color: var(--color-text-primary); font-weight: 600;">{{ $reservation->check_in->diffInDays($reservation->check_out) }}</p>
                    </div>
                </div>
            </div>

            {{-- Huéspedes --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Huéspedes</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
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
                        @foreach($parts as $part)
                            <div>
                                <p style="font-size: var(--text-base); color: var(--color-text-primary); font-weight: 500;">{{ $part }}</p>
                            </div>
                        @endforeach
                    @else
                        <div>
                            <p style="font-size: var(--text-base); color: var(--color-text-primary); font-weight: 500;">{{ $reservation->guests }} {{ $reservation->guests === 1 ? 'persona' : 'personas' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notas --}}
            @if($reservation->notes)
                <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                    <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Notas</h3>
                    <p style="font-size: var(--text-base); color: var(--color-text-primary); line-height: 1.6;">{{ $reservation->notes }}</p>
                </div>
            @endif

            {{-- Datos del Cliente --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Datos del Cliente</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Nombre</p>
                        <p style="font-size: var(--text-base); color: var(--color-text-primary); font-weight: 500;">{{ $reservation->user->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Email</p>
                        <p style="font-size: var(--text-base); color: var(--color-text-primary);">{{ $reservation->user->email ?? '—' }}</p>
                    </div>
                    @if($reservation->user->phone ?? false)
                        <div>
                            <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Teléfono</p>
                            <p style="font-size: var(--text-base); color: var(--color-text-primary);">{{ $reservation->user->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Precio --}}
            <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Precio Total</h3>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.25rem;">Total a pagar</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary);">{{ number_format($reservation->total_price, 2, ',', '.') }} €</p>
                    </div>
                    @if($reservation->status === 'paid')
                        <div style="text-align: right;">
                            <span class="badge badge-success" style="font-size: var(--text-sm);">Pagado</span>
                        </div>
                    @elseif($reservation->status === 'pending')
                        <div style="text-align: right;">
                            <span class="badge badge-warning" style="font-size: var(--text-sm);">Pendiente de pago</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div style="text-align: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn-action btn-action-primary" style="text-transform: none !important;">Volver al Dashboard</a>
        </div>
    </div>
</x-app-layout>