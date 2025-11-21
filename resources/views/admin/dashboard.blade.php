<x-app-layout>
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10 admin-slim-badges">
        <style>
            /* Badges: usar estilos globales neutros de staynest.css (sin overrides locales) */
            /* Admin: acciones como en Mis reservas (pequeños, oración, texto blanco, borde claro) */
            .admin-actions .btn-action {
                text-transform: none;
                letter-spacing: 0;
                font-weight: 600;
                font-size: var(--text-sm);
                line-height: 1.25rem;
                padding: 0.5rem 1.25rem;
                border-radius: 2px;
                color: #fff;
            }
            .admin-actions .btn-action.btn-action-secondary {
                background: transparent;
                border: 1px solid var(--color-accent) !important;
            }
            .admin-actions .btn-action.btn-action-danger {
                background: transparent;
                border: 1px solid var(--color-error) !important;
            }
            /* Admin: badges discretos, monocromos y accesibles */
            .admin-slim-badges .badge {
                text-transform: none;
                letter-spacing: 0;
                font-size: 0.75rem; /* 12px */
                padding: 0.25rem 0.5rem;
                border-radius: 2px;
                font-weight: 600;
                color: var(--color-text-primary);
                background: transparent !important;
                border: 1px solid var(--color-accent);
            }
            .admin-slim-badges .badge-success { border-color: var(--color-accent); }
            .admin-slim-badges .badge-warning { border-color: var(--color-accent); }
            .admin-slim-badges .badge-error { border-color: var(--color-accent); }
            .admin-slim-badges .badge-info { border-color: var(--color-accent); }
            .admin-actions .btn-action.btn-action-secondary:hover {
                background-color: rgba(77, 141, 148, 0.10);
                color: var(--color-accent);
                border-color: transparent !important;
            }
            .admin-actions .btn-action.btn-action-danger:hover {
                background-color: rgba(204, 89, 86, 0.15);
                color: var(--color-error);
                border-color: transparent !important;
            }
            /* Badges más pequeños y accesibles en Admin */
            .admin-slim-badges .badge {
                font-size: 0.6875rem; /* 11px */
                padding: 0.25rem 0.5rem; /* más compactos */
                border-radius: 2px;
                font-weight: 600;
                color: var(--color-text-primary); /* alto contraste */
                letter-spacing: 0.05em;
            }
            .admin-slim-badges .badge-success,
            .admin-slim-badges .badge-warning,
            .admin-slim-badges .badge-error,
            .admin-slim-badges .badge-info {
                background: transparent !important;
                border: 1px solid var(--color-accent);
            }
            .admin-slim-badges .badge-success { color: var(--color-text-primary); }
            .admin-slim-badges .badge-error { color: var(--color-text-primary); }
            .admin-slim-badges .badge-warning { color: var(--color-text-primary); }
            .admin-slim-badges .badge-info { color: var(--color-text-primary); }
        </style>
        
        {{-- Header centrado como otras páginas públicas --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Dashboard</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Resumen general de reservas y estadísticas.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        {{-- Widgets de estadísticas --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            {{-- Reservas activas --}}
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: var(--color-text-secondary); margin-top: 0.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div style="flex: 1;">
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Reservas Activas</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); line-height: 1;">{{ $stats['activeReservations'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Ingresos totales --}}
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: var(--color-text-secondary); margin-top: 0.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div style="flex: 1;">
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Ingresos Totales</p>
                        <p style="font-size: var(--text-2xl); font-weight: 600; color: var(--color-text-primary); line-height: 1;">{{ number_format($stats['totalRevenue'], 2, ',', '.') }} €</p>
                    </div>
                </div>
            </div>

            {{-- Ocupación del mes --}}
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: var(--color-text-secondary); margin-top: 0.125rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <div style="flex: 1;">
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.5rem;">Ocupación {{ now()->format('F') }}</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); line-height: 1;">{{ $stats['occupancyRate'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Próximas reservas --}}
        @if($stats['upcomingReservations']->isNotEmpty())
            <h2 style="font-size: var(--text-xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Próximas reservas</h2>
            <div style="margin-bottom: 2rem; background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px);">
                <div style="padding: 1.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 0;">
                        @foreach($stats['upcomingReservations'] as $upcoming)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 0; border-bottom: 1px solid var(--color-border-light);">
                                <div style="display: flex; align-items: center; gap: 0.875rem;">
                                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: var(--color-text-secondary); font-weight: 500; font-size: var(--text-sm);">{{ substr($upcoming->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p style="font-size: var(--text-base); font-weight: 500; color: var(--color-text-primary); margin-bottom: 0.125rem;">{{ $upcoming->user->name ?? 'Usuario' }}</p>
                                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
                                            {{ $upcoming->check_in->format('d/m/Y') }} - {{ $upcoming->check_out->format('d/m/Y') }}
                                            · {{ $upcoming->guests }} huésped(es)
                                        </p>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">{{ number_format($upcoming->total_price, 2, ',', '.') }} €</p>
                                    @if($upcoming->status === 'pending')
                                        <span class="badge badge-warning" style="font-size: var(--text-xs);">Pendiente</span>
                                    @elseif($upcoming->status === 'paid')
                                        <span class="badge badge-success" style="font-size: var(--text-xs);">Pagada</span>
                                    @elseif($upcoming->status === 'cancelled')
                                        <span class="badge badge-error" style="font-size: var(--text-xs);">Cancelada</span>
                                    @else
                                        <span class="badge badge-info" style="font-size: var(--text-xs);">{{ ucfirst($upcoming->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <h2 style="font-size: var(--text-xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Todas las reservas</h2>
        <div style="overflow: hidden; background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px);">
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: var(--text-base);">
                    <thead style="border-bottom: 2px solid var(--color-border-light);">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">ID</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Cliente</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Propiedad</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Fechas</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Huéspedes</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Total</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $r)
                            <tr style="border-bottom: none;">
                                <td style="padding: 1rem;">
                                    <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin: 0; font-family: monospace; font-weight: 600;">#{{ $r->id }}</p>
                                </td>
                                <td style="padding: 1rem; color: var(--color-text-primary);">{{ $r->user?->name ?? '—' }}</td>
                                <td style="padding: 1rem; color: var(--color-text-secondary);">{{ $r->property?->name ?? '—' }}</td>
                                <td style="padding: 1rem; color: var(--color-text-secondary); font-size: var(--text-sm);">{{ $r->check_in->format('d/m/Y') }} → {{ $r->check_out->format('d/m/Y') }}</td>
                                <td style="padding: 1rem; color: var(--color-text-secondary);">{{ $r->guests }}</td>
                                <td style="padding: 1rem; color: var(--color-text-primary); font-weight: 600; font-size: var(--text-lg);">{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                                <td style="padding: 1rem;">
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
                            </tr>
                            <tr style="border-bottom: 1px solid var(--color-border-light);">
                                <td colspan="7" style="padding: 0;">
                                    <div style="border-top: 1px solid var(--color-border-light); margin: 0 2rem; padding: 1rem 0 2rem 0;">
                                        <div class="admin-actions" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; justify-content: center;">
                                            <a href="{{ route('admin.reservations.edit', $r->id) }}" class="btn-action btn-action-secondary">Editar</a>

                                            @if ($r->status === 'pending')
                                                <form method="POST" action="{{ route('reservations.pay', $r->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-action btn-action-primary" onclick="return confirm('¿Marcar como pagada y generar factura?')">
                                                        Marcar pagada
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.reservations.cancel', $r->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-action btn-action-danger" onclick="return confirm('¿Cancelar esta reserva y reponer noches?')">
                                                        Cancelar
                                                    </button>
                                                </form>

                                            @elseif ($r->status === 'paid' && $r->invoice)
                                                <a href="{{ route('invoices.show', $r->invoice->number) }}" class="btn-action btn-action-secondary">Ver factura</a>
                                                <a href="{{ route('invoices.show', $r->invoice->number) }}?download=1" class="btn-action btn-action-secondary">Pdf</a>

                                                <form method="POST" action="{{ route('admin.reservations.refund', $r->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-action btn-action-danger" onclick="return confirm('Esto marcará la reserva como cancelada y registrará reembolso. ¿Continuar?')">
                                                        Reembolsar
                                                    </button>
                                                </form>

                                            @elseif ($r->status === 'paid')
                                                <span style="color: var(--color-text-muted); font-size: var(--text-sm);">Sin factura</span>

                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td style="padding: 2rem; text-align: center; color: var(--color-text-secondary);" colspan="7">No hay reservas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div style="padding: 1rem; border-top: 1px solid var(--color-border-light);">
                    {{ $reservations->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>