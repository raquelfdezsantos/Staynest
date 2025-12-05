<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 admin-slim-badges" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        <style>
            /* Badges discretos con diseño consistente */
            .admin-slim-badges .badge {
                font-size: 0.6875rem; /* 11px */
                padding: 0.25rem 0.5rem;
                border-radius: 2px;
                font-weight: 600;
                color: var(--color-text-primary);
                letter-spacing: 0.05em;
                height: 25.64px;
                box-sizing: border-box;
                text-transform: none;
            }
            .admin-slim-badges .badge-success {
                background: transparent !important;
                border: 1px solid var(--color-success);
            }
            .admin-slim-badges .badge-warning {
                background: transparent !important;
                border: 1px solid var(--color-warning);
            }
            .admin-slim-badges .badge-error {
                background: transparent !important;
                border: 1px solid var(--color-error);
            }
            .admin-slim-badges .badge-info {
                background: transparent !important;
                border: 1px solid var(--color-accent);
            }
        </style>
        
        {{-- Header centrado como otras páginas públicas --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Mis facturas</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Consulta y descarga todas tus facturas emitidas.</p>
        </header>

        @if($invoices->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <svg style="margin: 0 auto; width: 4rem; height: 4rem; color: var(--color-text-muted); margin-bottom: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p style="color: var(--color-text-secondary); margin-bottom: 2rem; font-size: var(--text-base);">
                    No tienes facturas aún.
                </p>
                <a href="{{ route('properties.reservar', $property->slug) }}" class="btn btn-primary">
                    Hacer una reserva
                </a>
            </div>
        @else
            {{-- Tabla de facturas en desktop --}}
            <div class="invoices-table" style="overflow: hidden; margin-top: 2rem; background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: var(--text-base); background: transparent;">
                        <thead style="border-bottom: 2px solid var(--color-border-light); background: transparent;">
                            <tr>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Nº Factura</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Fecha</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Alojamiento</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Estancia</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Importe</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr style="border-bottom: none;">
                                    <td style="padding: 1rem;">
                                        <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin: 0; font-family: monospace; font-weight: 600;">{{ $inv->number }}</p>
                                        @if($inv->amount < 0 || str_starts_with($inv->number, 'RECT-'))
                                            <p style="font-size: var(--text-xs); color: var(--color-text-muted); margin: 0; font-style: italic;">Rectificativa</p>
                                        @endif
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-secondary);">
                                        {{ optional($inv->issued_at)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-primary); font-weight: 500;">
                                        {{ $inv->reservation->property->name ?? '—' }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-secondary); font-size: var(--text-sm);">
                                        @php
                                            $details = $inv->details ?? [];
                                            $context = $details['context'] ?? '';
                                            
                                            // Para facturas rectificativas (INCREMENTO o DISMINUCIÓN), mostrar fechas NUEVAS
                                            if (in_array($context, ['increase_update', 'decrease_update']) && !empty($details['new_check_in'])) {
                                                $ci = \Carbon\Carbon::parse($details['new_check_in']);
                                                $co = \Carbon\Carbon::parse($details['new_check_out']);
                                            }
                                            // Para facturas iniciales, usar datos guardados en details
                                            elseif (!empty($details['check_in'])) {
                                                $ci = \Carbon\Carbon::parse($details['check_in']);
                                                $co = \Carbon\Carbon::parse($details['check_out']);
                                            }
                                            // Fallback: facturas antiguas sin details
                                            else {
                                                $ci = $inv->reservation->check_in;
                                                $co = $inv->reservation->check_out;
                                            }
                                        @endphp
                                        {{ $ci->format('d/m/Y') }} → {{ $co->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-primary); font-weight: 600; font-size: var(--text-lg);">
                                        @php($isRect = ($inv->amount < 0 || str_starts_with($inv->number, 'RECT-')))
                                        @if($isRect && is_array($inv->details))
                                            {{ number_format(($inv->details['new_total'] ?? ($inv->reservation->total_price ?? 0)), 2, ',', '.') }} €
                                        @else
                                            {{ number_format($inv->amount, 2, ',', '.') }} €
                                        @endif
                                    </td>
                                    <td style="padding: 1rem;">
                                        @php($status = strtolower($inv->reservation->status ?? ''))
                                        @if($status === 'paid')
                                            <span class="badge badge-success">Pagada</span>
                                        @elseif($status === 'cancelled')
                                            <span class="badge badge-error">Cancelada</span>
                                        @elseif($status === 'pending')
                                            <span class="badge badge-warning">Pendiente</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($inv->reservation->status ?? '—') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--color-border-light);">
                                    <td colspan="7" style="padding: 0;">
                                        <div style="border-top: 1px solid var(--color-border-light); margin: 0 2rem; padding: 1rem 0 2rem 0;">
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; justify-content: center;">
                                                <a href="{{ route('invoices.show', $inv->number) }}" class="btn-action btn-action-secondary"><span class="sn-sentence">Ver</span></a>
                                                <a href="{{ route('invoices.show', $inv->number) }}?download=1" class="btn-action btn-action-primary"><span class="sn-uppercase">PDF</span></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($invoices->hasPages())
                    <div style="padding: 1rem; border-top: 1px solid var(--color-border-light);">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

            {{-- Cards de facturas en móvil y tablets --}}
            <div class="invoices-cards" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem;">
                @foreach($invoices as $inv)
                    <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem;">
                        {{-- Header --}}
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border-light);">
                            <div>
                                <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin: 0; font-family: monospace; font-weight: 600;">{{ $inv->number }}</p>
                                @if($inv->amount < 0 || str_starts_with($inv->number, 'RECT-'))
                                    <p style="font-size: var(--text-xs); color: var(--color-text-muted); margin: 0; font-style: italic;">Rectificativa</p>
                                @endif
                            </div>
                            <div>
                                @php($statusCard = strtolower($inv->reservation->status ?? ''))
                                @if($statusCard === 'paid')
                                    <span class="badge badge-success">Pagada</span>
                                @elseif($statusCard === 'cancelled')
                                    <span class="badge badge-error">Cancelada</span>
                                @elseif($statusCard === 'pending')
                                    <span class="badge badge-warning">Pendiente</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst($inv->reservation->status ?? '—') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Detalles --}}
                        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--color-text-secondary); font-size: var(--text-sm);">Fecha:</span>
                                <span style="color: var(--color-text-primary); font-weight: 500;">{{ optional($inv->issued_at)->format('d/m/Y') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--color-text-secondary); font-size: var(--text-sm);">Alojamiento:</span>
                                <span style="color: var(--color-text-primary); font-weight: 500; text-align: right;">{{ $inv->reservation->property->name ?? '—' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--color-text-secondary); font-size: var(--text-sm);">Estancia:</span>
                                <span style="color: var(--color-text-primary); font-size: var(--text-sm); text-align: right;">
                                    {{ optional($inv->reservation->check_in)->format('d/m/Y') }} &rarr; {{ optional($inv->reservation->check_out)->format('d/m/Y') }}
                                </span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid var(--color-border-light);">
                                <span style="color: var(--color-text-secondary); font-weight: 600;">Importe:</span>
                                <span style="color: var(--color-accent); font-weight: 700; font-size: var(--text-xl);">
                                    @php($isRectCard = ($inv->amount < 0 || str_starts_with($inv->number, 'RECT-')))
                                    @if($isRectCard && is_array($inv->details))
                                        {{ number_format(($inv->details['new_total'] ?? ($inv->reservation->total_price ?? 0)), 2, ',', '.') }} €
                                    @else
                                        {{ number_format($inv->amount, 2, ',', '.') }} €
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="{{ route('invoices.show', $inv->number) }}" class="btn-action btn-action-secondary" style="flex: 1;"><span class="sn-sentence">Ver</span></a>
                            <a href="{{ route('invoices.show', $inv->number) }}?download=1" class="btn-action btn-action-primary" style="flex: 1;"><span class="sn-uppercase">PDF</span></a>
                        </div>
                    </div>
                @endforeach

                {{-- Paginación para móvil --}}
                @if($invoices->hasPages())
                    <div style="margin-top: 1rem;">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        .btn-action {
            height: 36px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Mostrar tabla en desktop, cards en móvil/tablet */
        @media (min-width: 769px) {
            .invoices-table {
                display: block !important;
            }
            .invoices-cards {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .invoices-table {
                display: none !important;
            }
            .invoices-cards {
                display: flex !important;
            }
        }
    </style>
</x-app-layout>