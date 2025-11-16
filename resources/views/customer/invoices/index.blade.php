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
            {{-- Tabla minimalista --}}
            <div style="overflow-x: auto; margin-top: 2rem;">
                <table class="table-admin">
                    <thead>
                        <tr>
                            <th>Nº Factura</th>
                            <th>Fecha emisión</th>
                            <th>Alojamiento</th>
                            <th>Fechas estancia</th>
                            <th>Importe</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $inv)
                            <tr>
                                <td>{{ $inv->number }}</td>
                                <td>{{ optional($inv->issued_at)->format('d/m/Y') }}</td>
                                <td>{{ $inv->reservation->property->name ?? '—' }}</td>
                                <td>
                                    {{ $inv->reservation->check_in->format('d/m/Y') }} → {{ $inv->reservation->check_out->format('d/m/Y') }}
                                </td>
                                <td>{{ number_format($inv->amount, 2, ',', '.') }} €</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="{{ route('invoices.show', $inv->number) }}" class="btn-action btn-action-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                            Ver
                                        </a>
                                        <a href="{{ route('invoices.show', $inv->number) }}?download=1" class="btn-action btn-action-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.875rem;">
                                            Descargar PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginación --}}
                @if($invoices->hasPages())
                    <div style="margin-top: 2rem;">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>