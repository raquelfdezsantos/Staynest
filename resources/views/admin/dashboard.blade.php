<x-app-layout>
    <div class="max-w-7xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Dashboard
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">
                Resumen general de reservas y estadísticas.
            </p>
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
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: var(--color-accent); border-radius: 2px; padding: 0.75rem; flex-shrink: 0;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.25rem;">Reservas Activas</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary);">{{ $stats['activeReservations'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Ingresos totales --}}
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: var(--color-success); border-radius: 2px; padding: 0.75rem; flex-shrink: 0;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.25rem;">Ingresos Totales</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary);">{{ number_format($stats['totalRevenue'], 2, ',', '.') }} €</p>
                    </div>
                </div>
            </div>

            {{-- Ocupación del mes --}}
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: #3b82f6; border-radius: 2px; padding: 0.75rem; flex-shrink: 0;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 0.25rem;">Ocupación {{ now()->format('F') }}</p>
                        <p style="font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary);">{{ $stats['occupancyRate'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Próximas reservas --}}
        @if($stats['upcomingReservations']->isNotEmpty())
            <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Próximas Reservas</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($stats['upcomingReservations'] as $upcoming)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(77, 141, 148, 0.05); border-radius: 2px;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: rgba(77, 141, 148, 0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span style="color: var(--color-accent); font-weight: 600; font-size: var(--text-sm);">{{ substr($upcoming->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p style="font-size: var(--text-sm); font-weight: 500; color: var(--color-text-primary);">{{ $upcoming->user->name ?? 'Usuario' }}</p>
                                    <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
                                        {{ $upcoming->check_in->format('d/m/Y') }} - {{ $upcoming->check_out->format('d/m/Y') }}
                                        · {{ $upcoming->guests }} huésped(es)
                                    </p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: var(--text-sm); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">{{ number_format($upcoming->total_price, 2, ',', '.') }} €</p>
                                <span class="badge {{ $upcoming->status === 'paid' ? 'badge-success' : 'badge-warning' }}" style="font-size: var(--text-xs);">
                                    {{ $upcoming->status === 'paid' ? 'Pagada' : 'Pendiente' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--color-border-light);">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">Todas las Reservas</h3>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: var(--text-sm);">
                    <thead style="background: rgba(77, 141, 148, 0.08); border-bottom: 2px solid var(--color-border-light);">
                        <tr>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">ID</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Cliente</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Propiedad</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Check-in</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Check-out</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Huéspedes</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Total</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Estado</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $r)
                            <tr style="border-bottom: 1px solid var(--color-border-light);">
                                <td style="padding: 0.75rem; color: var(--color-text-primary); font-family: monospace; font-weight: 600;">#{{ $r->id }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-primary);">{{ $r->user?->name ?? '—' }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-secondary);">{{ $r->property?->name ?? '—' }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-secondary); font-size: var(--text-xs);">{{ $r->check_in->format('d/m/Y') }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-secondary); font-size: var(--text-xs);">{{ $r->check_out->format('d/m/Y') }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-secondary);">{{ $r->guests }}</td>
                                <td style="padding: 0.75rem; color: var(--color-text-primary); font-weight: 600;">{{ number_format($r->total_price, 2, ',', '.') }} €</td>
                                <td style="padding: 0.75rem;">
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
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                        <a href="{{ route('admin.reservations.edit', $r->id) }}" style="color: var(--color-accent); text-decoration: none; font-weight: 500; font-size: var(--text-sm);" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Editar</a>

                                        @if ($r->status === 'pending')
                                            <form method="POST" action="{{ route('reservations.pay', $r->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="padding: 0.375rem 0.75rem; border-radius: 2px; background: var(--color-accent); color: #fff; border: none; font-size: var(--text-sm); font-weight: 500; cursor: pointer; transition: background var(--transition-fast);" onmouseover="this.style.backgroundColor='var(--color-accent-hover)'" onmouseout="this.style.backgroundColor='var(--color-accent)'" onclick="return confirm('¿Marcar como pagada y generar factura?')">
                                                    Marcar pagada
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.reservations.cancel', $r->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="padding: 0.375rem 0.75rem; border-radius: 2px; background: var(--color-error); color: #fff; border: none; font-size: var(--text-sm); font-weight: 500; cursor: pointer; transition: background var(--transition-fast);" onmouseover="this.style.backgroundColor='#d87876'" onmouseout="this.style.backgroundColor='var(--color-error)'" onclick="return confirm('¿Cancelar esta reserva y reponer noches?')">
                                                    Cancelar
                                                </button>
                                            </form>

                                        @elseif ($r->status === 'paid' && $r->invoice)
                                            <a style="color: var(--color-accent); text-decoration: none; font-weight: 500; font-size: var(--text-sm);" href="{{ route('invoices.show', $r->invoice->number) }}" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Ver factura</a>
                                            <a style="color: var(--color-accent); text-decoration: none; font-weight: 500; font-size: var(--text-sm);" href="{{ route('invoices.show', $r->invoice->number) }}?download=1" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">PDF</a>

                                            <form method="POST" action="{{ route('admin.reservations.refund', $r->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" style="color: var(--color-error); background: none; border: none; font-weight: 500; font-size: var(--text-sm); cursor: pointer; text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" onclick="return confirm('Esto marcará la reserva como cancelada y registrará reembolso. ¿Continuar?')">
                                                    Reembolsar
                                                </button>
                                            </form>

                                        @elseif ($r->status === 'paid')
                                            <span style="color: var(--color-text-muted); font-size: var(--text-sm);">Sin factura</span>

                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td style="padding: 2rem; text-align: center; color: var(--color-text-secondary);" colspan="9">No hay reservas.</td>
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