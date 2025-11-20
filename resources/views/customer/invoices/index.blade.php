<x-app-layout>
    <div class="max-w-5xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header simple --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Mis facturas
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">
                Consulta y descarga todas tus facturas emitidas.
            </p>
        </header>

        @if($invoices->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <svg style="margin: 0 auto; width: 4rem; height: 4rem; color: var(--color-text-muted); margin-bottom: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p style="color: var(--color-text-secondary); margin-bottom: 2rem; font-size: var(--text-base);">
                    No tienes facturas aún.
                </p>
                <a href="{{ route('reservar') }}" class="btn btn-primary">
                    Hacer una reserva
                </a>
            </div>
        @else
            {{-- Tabla de facturas (estilo minimal: sin fondo ni borde contenedor) --}}
            <div style="overflow: hidden; margin-top: 2rem;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: var(--text-sm); background: transparent;">
                        <thead style="border-bottom: 1px solid var(--color-border-light); background: transparent;">
                            <tr>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Nº Factura</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Fecha</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Alojamiento</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Estancia</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Importe</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600; font-size: var(--text-sm); text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr style="border-bottom: 1px solid var(--color-border-light);">
                                    <td style="padding: 1rem; color: var(--color-text-primary); font-family: monospace; font-weight: 600;">
                                        {{ $inv->number }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-secondary);">
                                        {{ optional($inv->issued_at)->format('d/m/Y') }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-primary); font-weight: 500;">
                                        {{ $inv->reservation->property->name ?? '—' }}
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-secondary); font-size: var(--text-xs);">
                                        {{ $inv->reservation->check_in->format('d/m/Y') }}<br>
                                        <span style="opacity: 0.6;">→ {{ $inv->reservation->check_out->format('d/m/Y') }}</span>
                                    </td>
                                    <td style="padding: 1rem; text-align: right; color: var(--color-text-primary); font-weight: 600; font-size: var(--text-base);">
                                        {{ number_format($inv->amount, 2, ',', '.') }} €
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                            <a href="{{ route('invoices.show', $inv->number) }}" class="btn-action btn-action-secondary"><span class="sn-sentence">Ver</span></a>
                                            <a href="{{ route('invoices.show', $inv->number) }}?download=1" class="btn-action btn-action-primary"><span class="sn-uppercase">PDF</span></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación (sin fondo) --}}
                @if($invoices->hasPages())
                    <div style="padding: 1rem; border-top: 1px solid var(--color-border-light);">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>