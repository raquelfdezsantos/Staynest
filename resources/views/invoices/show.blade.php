@extends('layouts.app')
@section('title','Factura ' . $invoice->number)
@section('content')
    @php
        $res = $invoice->reservation;
        $details = $invoice->details ?? [];
        $isRect = ($invoice->amount < 0 || (is_array($details) && in_array($details['context'] ?? '', ['decrease_update', 'increase_update', 'balance_payment'])));
        
        // Para facturas rectificativas con cambios, mostrar datos previos
        if ($isRect && !empty($details['previous_check_in'])) {
            $displayCheckIn = \Carbon\Carbon::parse($details['previous_check_in']);
            $displayCheckOut = \Carbon\Carbon::parse($details['previous_check_out']);
            $displayAmount = $details['previous_total'] ?? abs($invoice->amount);
            $displayGuests = $details['previous_guests'] ?? $res->guests;
            $displayAdults = $details['previous_adults'] ?? ($res->adults ?? 0);
            $displayChildren = $details['previous_children'] ?? ($res->children ?? 0);
            $displayPets = $details['previous_pets'] ?? ($res->pets ?? 0);
        } 
        // Para facturas iniciales o de balance, usar los datos guardados en details
        elseif (!empty($details['check_in'])) {
            $displayCheckIn = \Carbon\Carbon::parse($details['check_in']);
            $displayCheckOut = \Carbon\Carbon::parse($details['check_out']);
            $displayAmount = $invoice->amount;
            $displayGuests = $details['guests'] ?? $res->guests;
            $displayAdults = $details['adults'] ?? ($res->adults ?? 0);
            $displayChildren = $details['children'] ?? ($res->children ?? 0);
            $displayPets = $details['pets'] ?? ($res->pets ?? 0);
        }
        // Fallback: usar datos actuales de la reserva (facturas antiguas sin details)
        else {
            $displayCheckIn = $res->check_in;
            $displayCheckOut = $res->check_out;
            $displayAmount = $invoice->amount;
            $displayGuests = $res->guests;
            $displayAdults = $res->adults ?? 0;
            $displayChildren = $res->children ?? 0;
            $displayPets = $res->pets ?? 0;
        }
        
        $parts = [];
        $ad = (int)$displayAdults;
        $ch = (int)$displayChildren;
        $pt = (int)$displayPets;
        if($ad>0) $parts[] = $ad.' '.($ad===1?'adulto':'adultos');
        if($ch>0) $parts[] = $ch.' '.($ch===1?'niño':'niños');
        if($pt>0) $parts[] = $pt.' '.($pt===1?'mascota':'mascotas');
    @endphp

    <div class="invoice-page max-w-5xl mx-auto px-4 py-10" style="overflow-x: hidden; width: 100%;">

        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">
                @if($invoice->amount < 0 || str_starts_with($invoice->number, 'RECT-'))
                    Factura Rectificativa {{ $invoice->number }}
                @else
                    Factura {{ $invoice->number }}
                @endif
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Emitida: {{ optional($invoice->issued_at)->format('d/m/Y H:i') }}</p>
            @php
                $status = strtolower($invoice->reservation->status ?? '');
            @endphp
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

        <div class="invoice-detail-grid grid md:grid-cols-3 gap-6 mb-10">
            <div class="md:col-span-2 space-y-6">
                <section class="invoice-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Datos</h2>
                    <div class="invoice-data-grid grid sm:grid-cols-2 gap-5 text-sm">
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
                            @php
                                $p = $invoice->reservation->property;
                            @endphp
                            <div class="text-sm" style="color: var(--color-text-secondary);">
                                <div class="mb-2"><strong>Nombre:</strong> {{ $p->name }}</div>
                                <div class="mb-2"><strong>Licencia turística:</strong> {{ $p->tourism_license ?? '—' }}</div>
                                <div class="mb-2"><strong>Registro alquiler:</strong> {{ $p->rental_registration ?? '—' }}</div>
                                <div class="mb-2"><strong>Dirección:</strong> {{ $p->address ?? '—' }}, {{ $p->postal_code ?? '' }} {{ $p->city ?? '' }} {{ $p->province ? '(' . $p->province . ')' : '' }}</div>
                                <div class="mb-2"><strong>Propietario:</strong> {{ $p->owner_name ?? '—' }}</div>
                                <div class="mb-2"><strong>CIF/NIF:</strong> {{ $p->owner_tax_id ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                    <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Resumen</h2>
                    
                    {{-- Tabla para desktop --}}
                    <div class="invoice-summary-table overflow-hidden text-sm">
                         <table class="w-full text-sm" style="border-collapse: collapse;">
                            <thead>
                                <tr class="text-center">
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:left; color: var(--color-text-secondary);">Concepto</th>
                                    <th class="py-3 font-medium" style="font-size: var(--text-base); text-align:center; color: var(--color-text-secondary);">Fechas</th>
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
                                     <td class="py-3 text-center" style="color: var(--color-text-secondary);">{{ $displayCheckIn->format('d/m/Y') }} → {{ $displayCheckOut->format('d/m/Y') }}</td>
                                     <td class="py-3 text-center" style="color: var(--color-text-secondary);">
                                       @if(count($parts))
                                         {{ implode(', ',$parts) }} (total: {{ $displayGuests }})
                                       @else
                                         {{ $displayGuests }} {{ $displayGuests === 1 ? 'huésped' : 'huéspedes' }}
                                       @endif
                                     </td>
                                     <td class="py-3 text-right font-semibold" style="color: var(--color-text-secondary);">{{ number_format($displayAmount, 2, ',', '.') }} €</td>
                                </tr>
                                @if($isRect && !empty($invoice->details['new_check_in']))
                                <tr>
                                    <td class="py-3 pr-4" style="color: var(--color-text-secondary);">Cambios aplicados</td>
                                    <td class="py-3 text-center" style="color: var(--color-text-secondary);">
                                      @if($invoice->details['previous_check_in'] !== $invoice->details['new_check_in'] || $invoice->details['previous_check_out'] !== $invoice->details['new_check_out'])
                                        {{ \Carbon\Carbon::parse($invoice->details['new_check_in'])->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($invoice->details['new_check_out'])->format('d/m/Y') }}
                                      @else
                                        —
                                      @endif
                                    </td>
                                    <td class="py-3 text-center" style="color: var(--color-text-secondary);">
                                      @php
                                        $newAdults = (int)($invoice->details['new_adults'] ?? 0);
                                        $newChildren = (int)($invoice->details['new_children'] ?? 0);
                                        $newPets = (int)($invoice->details['new_pets'] ?? 0);
                                        $newGuests = (int)($invoice->details['new_guests'] ?? 0);
                                        $guestsChanged = ($displayAdults !== $newAdults || $displayChildren !== $newChildren || $displayPets !== $newPets);
                                        
                                        $newParts = [];
                                        if($newAdults>0) $newParts[] = $newAdults.' '.($newAdults===1?'adulto':'adultos');
                                        if($newChildren>0) $newParts[] = $newChildren.' '.($newChildren===1?'niño':'niños');
                                        if($newPets>0) $newParts[] = $newPets.' '.($newPets===1?'mascota':'mascotas');
                                      @endphp
                                      @if($guestsChanged)
                                        @if(count($newParts))
                                          {{ implode(', ', $newParts) }} (total: {{ $newGuests }})
                                        @else
                                          {{ $newGuests }} {{ $newGuests === 1 ? 'huésped' : 'huéspedes' }}
                                        @endif
                                      @else
                                        —
                                      @endif
                                    </td>
                                    <td class="py-3 text-right font-semibold" style="color: {{ $invoice->amount < 0 ? '#dc3545' : '#28a745' }};">
                                      {{ $invoice->amount < 0 ? '-' : '+' }}{{ number_format(abs($invoice->details['difference'] ?? $invoice->amount), 2, ',', '.') }} €
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="border-t" style="border-color: rgba(var(--color-border-rgb), 0.1); font-size:1rem;">
                                <tr>
                                    <td colspan="3" class="py-3 font-medium text-left" style="color: var(--color-text-secondary);">Total</td>
                                    <td class="py-3 text-right font-bold" style="color: var(--color-text-primary);">
                                      {{ number_format($isRect && !empty($invoice->details['new_total']) ? $invoice->details['new_total'] : $invoice->amount, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Cards para móvil/tablet --}}
                    <div class="invoice-summary-cards" style="display: none;">
                        <div style="background: rgba(var(--color-bg-card-rgb), 0.5); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                            <div style="margin-bottom: 1rem;">
                                <div style="color: var(--color-text-primary); font-weight: 500; margin-bottom: 0.25rem;">Reserva {{ $res->code }}</div>
                                <div style="color: var(--color-text-primary); font-weight: 500;">{{ $res->property->name }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: var(--text-sm);">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--color-text-secondary);">Fechas:</span>
                                    <span style="color: var(--color-text-secondary); text-align: right;">{{ $displayCheckIn->format('d/m/Y') }} &rarr; {{ $displayCheckOut->format('d/m/Y') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--color-text-secondary);">Huéspedes:</span>
                                    <span style="color: var(--color-text-secondary); text-align: right;">
                                        @if(count($parts))
                                            {{ implode(', ',$parts) }} (total: {{ $displayGuests }})
                                        @else
                                            {{ $displayGuests }} {{ $displayGuests === 1 ? 'huésped' : 'huéspedes' }}
                                        @endif
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 1px solid rgba(var(--color-border-rgb), 0.1);">
                                    <span style="color: var(--color-text-secondary); font-weight: 500;">Importe:</span>
                                    <span style="color: var(--color-text-secondary); font-weight: 600;">{{ number_format($displayAmount, 2, ',', '.') }} €</span>
                                </div>
                            </div>
                        </div>

                        @if($isRect && !empty($invoice->details['new_check_in']))
                        <div style="background: rgba(var(--color-bg-card-rgb), 0.5); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                            <div style="color: var(--color-text-secondary); font-weight: 500; margin-bottom: 0.75rem;">Cambios aplicados</div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: var(--text-sm);">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--color-text-secondary);">Nuevas fechas:</span>
                                    <span style="color: var(--color-text-secondary); text-align: right;">
                                        @if($invoice->details['previous_check_in'] !== $invoice->details['new_check_in'] || $invoice->details['previous_check_out'] !== $invoice->details['new_check_out'])
                                            {{ \Carbon\Carbon::parse($invoice->details['new_check_in'])->format('d/m/Y') }} &rarr; {{ \Carbon\Carbon::parse($invoice->details['new_check_out'])->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--color-text-secondary);">Nuevos huéspedes:</span>
                                    <span style="color: var(--color-text-secondary); text-align: right;">
                                        @php
                                            $newAdults = (int)($invoice->details['new_adults'] ?? 0);
                                            $newChildren = (int)($invoice->details['new_children'] ?? 0);
                                            $newPets = (int)($invoice->details['new_pets'] ?? 0);
                                            $newGuests = (int)($invoice->details['new_guests'] ?? 0);
                                            $guestsChanged = ($displayAdults !== $newAdults || $displayChildren !== $newChildren || $displayPets !== $newPets);
                                            
                                            $newParts = [];
                                            if($newAdults>0) $newParts[] = $newAdults.' '.($newAdults===1?'adulto':'adultos');
                                            if($newChildren>0) $newParts[] = $newChildren.' '.($newChildren===1?'niño':'niños');
                                            if($newPets>0) $newParts[] = $newPets.' '.($newPets===1?'mascota':'mascotas');
                                        @endphp
                                        @if($guestsChanged)
                                            @if(count($newParts))
                                                {{ implode(', ', $newParts) }} (total: {{ $newGuests }})
                                            @else
                                                {{ $newGuests }} {{ $newGuests === 1 ? 'huésped' : 'huéspedes' }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 1px solid rgba(var(--color-border-rgb), 0.1);">
                                    <span style="color: var(--color-text-secondary); font-weight: 500;">Diferencia:</span>
                                    <span style="font-weight: 600; color: {{ $invoice->amount < 0 ? '#dc3545' : '#28a745' }};">
                                        {{ $invoice->amount < 0 ? '-' : '+' }}{{ number_format(abs($invoice->details['difference'] ?? $invoice->amount), 2, ',', '.') }} €
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div style="background: rgba(var(--color-accent-rgb), 0.1); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-sm); padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--color-text-primary); font-weight: 600; font-size: var(--text-lg);">Total</span>
                                <span style="color: var(--color-text-primary); font-weight: 700; font-size: var(--text-xl);">
                                    {{ number_format($isRect && !empty($invoice->details['new_total']) ? $invoice->details['new_total'] : $invoice->amount, 2, ',', '.') }} €
                                </span>
                            </div>
                        </div>
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
                        @php
                            $backUrl = Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('invoices.index');
                        @endphp
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

            /* Evitar desbordamiento en todos los tamaños */
            .invoice-page {
                overflow-x: hidden;
            }
            
            .invoice-card,
            .invoice-card * {
                max-width: 100%;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
            
            .invoice-card div {
                min-width: 0;
            }

            /* Responsive: mostrar tabla en desktop, cards en móvil/tablet */
            @media (min-width: 769px) {
                .invoice-summary-table {
                    display: block !important;
                }
                .invoice-summary-cards {
                    display: none !important;
                }
            }

            @media (max-width: 768px) {
                .invoice-summary-table {
                    display: none !important;
                }
                .invoice-summary-cards {
                    display: block !important;
                }
                
                /* Layout de una columna en móvil */
                .invoice-detail-grid {
                    grid-template-columns: 1fr !important;
                }
                
                /* Datos en una columna también */
                .invoice-data-grid {
                    grid-template-columns: 1fr !important;
                }
                
                /* Reducir padding en cards pequeñas */
                .invoice-card {
                    padding: 1rem !important;
                }
            }
        </style>
    </div>
@endsection
