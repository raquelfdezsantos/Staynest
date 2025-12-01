@extends('layouts.app')

@section('title', 'Reservar')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Calendario Flatpickr - Modo Oscuro */
        html[data-theme="dark"] .flatpickr-calendar {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        html[data-theme="dark"] .flatpickr-months {
            background: var(--color-bg-card);
            border-bottom: 1px solid var(--color-border-light);
        }
        
        html[data-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months,
        html[data-theme="dark"] .flatpickr-current-month input.cur-year {
            color: var(--color-text-primary);
            background: var(--color-bg-secondary);
        }
        
        html[data-theme="dark"] .flatpickr-weekdays {
            background: var(--color-bg-card);
        }
        
        html[data-theme="dark"] span.flatpickr-weekday {
            color: var(--color-text-secondary);
        }
        
        html[data-theme="dark"] .flatpickr-day {
            color: var(--color-text-primary);
        }
        
        html[data-theme="dark"] .flatpickr-day.unavailable,
        html[data-theme="dark"] .flatpickr-day.unavailable:hover,
        html[data-theme="dark"] .flatpickr-day.flatpickr-disabled,
        html[data-theme="dark"] .flatpickr-day.flatpickr-disabled:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
        html[data-theme="dark"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
            background: rgba(77,141,148,0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="dark"] .flatpickr-day.selected,
        html[data-theme="dark"] .flatpickr-day.startRange,
        html[data-theme="dark"] .flatpickr-day.endRange {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        html[data-theme="dark"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }
        
        html[data-theme="dark"] .flatpickr-months .flatpickr-prev-month svg,
        html[data-theme="dark"] .flatpickr-months .flatpickr-next-month svg {
            fill: var(--color-text-primary);
        }
        
        /* Modo Claro */
        html[data-theme="light"] .flatpickr-calendar {
            background: #d1d1d1;
            border: 1px solid #e0e0e0;
        }
        
        html[data-theme="light"] .flatpickr-months {
            background: #d1d1d1;
        }
        
        html[data-theme="light"] .flatpickr-weekdays {
            background: #d1d1d1;
        }
        
        html[data-theme="light"] .flatpickr-day {
            color: #222;
        }
        
        html[data-theme="light"] .flatpickr-day.unavailable,
        html[data-theme="light"] .flatpickr-day.unavailable:hover,
        html[data-theme="light"] .flatpickr-day.flatpickr-disabled,
        html[data-theme="light"] .flatpickr-day.flatpickr-disabled:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
        html[data-theme="light"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
            background: rgba(77,141,148,0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="light"] .flatpickr-day.selected,
        html[data-theme="light"] .flatpickr-day.startRange,
        html[data-theme="light"] .flatpickr-day.endRange {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        html[data-theme="light"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }
        
        /* Ajustar el grid de días para el nuevo tamaño */
        .flatpickr-days {
            width: 308px !important;
        }
        
        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: center !important;
        }
        
        /* Hacer los días cuadrados (no círculos) */
        .flatpickr-day {
            max-width: 38px !important;
            max-height: 38px !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
            margin: 2px !important;
            border-radius: 2px !important;
            border: none !important;
        }
        
        /* Días de meses anterior/posterior más apagados */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            opacity: 0.4 !important;
        }
        
        /* Asegurar opacidad en días de otros meses */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            opacity: 0.4 !important;
        }

        /* Refuerzo final anti-hover (por si carga otra hoja después) */
        .flatpickr-day.unavailable,
        .flatpickr-day.unavailable:hover,
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            border-color: transparent !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
    </style>
    
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3">Reservar</h1>
            <p class="text-neutral-300">
                Completa los datos y comprueba el resumen de tu reserva al instante.
            </p>
        </header>

        <!-- Mensaje de error -->
        <div id="error-message" class="alert alert-error mb-4 hidden">
            <strong>Revisa lo siguiente:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                <li id="error-text"></li>
            </ul>
        </div>

        <div class="grid md:grid-cols-3 gap-8" style="align-items: start;">
            <!-- Formulario -->
            <form 
                class="md:col-span-2 space-y-6 reservar-form-mobile"
                method="POST" 
                action="@guest {{ route('reservas.prepare') }} @else {{ route('reservas.store') }} @endguest" 
                id="reservationForm"
            >
                @csrf

                {{-- este alojamiento (si solo tienes uno) --}}
                <input type="hidden" name="property_id" value="{{ $property->id ?? 1 }}">
                {{-- guests se rellena en JS (adultos + niños) --}}
                <input type="hidden" name="guests" id="guests">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Fecha de entrada</label>
                        <input 
                            type="text" 
                            id="check_in"
                            name="check_in"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            placeholder="YYYY-MM-DD"
                            value="{{ old('check_in', $prefill['check_in'] ?? '') }}"
                        >
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Fecha de salida</label>
                        <input 
                            type="text" 
                            id="check_out"
                            name="check_out"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            placeholder="YYYY-MM-DD"
                            value="{{ old('check_out', $prefill['check_out'] ?? '') }}"
                        >
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Adultos</label>
                        <select 
                            id="adults"
                            name="adults"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]"
                        >
                            @php($ad = old('adults', $prefill['adults'] ?? 2))
                            @for($i = 1; $i <= $property->capacity; $i++)
                                <option value="{{ $i }}" @selected($ad==$i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Niños</label>
                        <select 
                            id="children"
                            name="children"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]"
                        >
                            @php($ch = old('children', $prefill['children'] ?? 0))
                            @for($i = 0; $i <= $property->capacity; $i++)
                                <option value="{{ $i }}" @selected($ch==$i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-neutral-300 mb-1">Mascotas</label>
                        <select 
                            id="pets"
                            name="pets"
                            class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]"
                            @if(!$property->allows_pets) disabled @endif
                        >
                            @php($pt = old('pets', $prefill['pets'] ?? 0))
                            <option value="0" @selected($pt==0)>No</option>
                            @if($property->allows_pets)
                                <option value="1" @selected($pt==1)>1</option>
                                <option value="2" @selected($pt==2)>2</option>
                            @endif
                        </select>
                        @if($property->allows_pets)
                            <p id="pets-free-message" class="hidden" style="font-size: var(--text-xs); color: var(--color-success); margin-top: 0.25rem;">¡Las mascotas se alojan gratis!</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-neutral-300 mb-1">Notas</label>
                    <textarea 
                        id="notes"
                        name="notes"
                        class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 h-28 shadow-sm focus:outline-none focus:ring-[1px] focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                        placeholder="Cuéntanos cualquier necesidad especial"
                    >{{ old('notes', $prefill['notes'] ?? '') }}</textarea>
                </div>
                <div class="pt-2 reservar-btn-mobile">
                    <button 
                        type="submit"
                        class="bg-[color:var(--color-accent)] hover:bg-[color:var(--color-accent-hover)] text-white text-sm font-semibold px-5 py-2"
                        style="border-radius: 2px;"
                    >
                        Reservar ahora
                    </button>
                </div>
            </form>

            <!-- Resumen - alineado con los inputs de fechas -->
            <aside class="space-y-4 reservar-aside-mobile" style="margin-top: 3rem;">
                <div class="bg-neutral-800 border border-neutral-700 p-4 reservar-resumen-mobile" style="border-radius:var(--radius-base);">
                    <h3 class="font-semibold mb-2">Resumen</h3>
                    <ul class="text-sm text-neutral-300 space-y-1">
                        <li>Fechas: <span class="text-neutral-400" id="summary-dates">—</span></li>
                        <li>Huéspedes: <span class="text-neutral-400" id="summary-guests">2 adultos</span></li>
                        <li>Noches: <span class="text-neutral-400" id="summary-nights">—</span></li>
                    </ul>
                    <div class="mt-3 pt-3 border-t border-neutral-700">
                        <p class="text-sm">Total estimado</p>
                        <p class="text-2xl font-serif" id="summary-total">—</p>
                    </div>
                </div>
                <div class="text-xs text-neutral-400 reservar-msg-mobile">
                    La disponibilidad y precio se calcularán aquí. Después conectaremos con el flujo de reserva y pago
                    existente.
                </div>
            </aside>
        </div>
        <style>
        @media (max-width: 540px) {
            /* En móvil, el form es flex column para que los elementos dentro se ordenen bien */
            .reservar-form-mobile { display: flex; flex-direction: column; }
            /* Reducir el margen superior del aside para que esté más pegado al form, igual a gap-4 */
            .reservar-aside-mobile { margin-top: 0.5rem !important; }
        }
        </style>

        {{-- Script Flatpickr --}}
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const blockedDates = @json($blockedDates ?? []);
                const checkinDates = @json($checkinDates ?? []);
                const rates = @json($rates ?? []);
                const maxCapacity = {{ $property->capacity ?? 4 }};
                let pickersReady = false;

                // Actualizar resumen de huéspedes
                function updateGuests() {
                    const adults = parseInt(document.getElementById('adults').value);
                    const children = parseInt(document.getElementById('children').value);
                    const pets = parseInt(document.getElementById('pets').value);
                    const totalGuests = adults + children;

                    // Actualizar texto del resumen
                    let text = adults + ' adulto' + (adults !== 1 ? 's' : '');
                    if (children > 0) {
                        text += ', ' + children + ' niño' + (children !== 1 ? 's' : '');
                    }
                    if (pets > 0) {
                        text += ', ' + pets + ' mascota' + (pets !== 1 ? 's' : '');
                    }
                    document.getElementById('summary-guests').textContent = text;

                    // Mostrar mensaje de mascotas gratis si hay mascotas
                    const petsMsg = document.getElementById('pets-free-message');
                    if (petsMsg) {
                        if (pets > 0) petsMsg.classList.remove('hidden');
                        else petsMsg.classList.add('hidden');
                    }

                    // Validar capacidad máxima
                    if (totalGuests > maxCapacity) {
                        showError(`La capacidad máxima es de ${maxCapacity} personas.`);
                    } else {
                        hideError();
                    }

                    if (pickersReady) { updateTotal(); }
                }

                document.getElementById('adults').addEventListener('change', updateGuests);
                document.getElementById('children').addEventListener('change', updateGuests);
                document.getElementById('pets').addEventListener('change', updateGuests);

                // (Inicialización del resumen se hará tras crear los pickers)

                // Configurar Flatpickr para check-in
                const checkInPicker = flatpickr('#check_in', {
                    locale: 'es',
                    minDate: 'today',
                    dateFormat: 'Y-m-d',
                    defaultDate: '{{ old('check_in', $prefill['check_in'] ?? '') }}',
                    disable: [
                        function (date) {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const day = String(date.getDate()).padStart(2, '0');
                            const dateStr = `${year}-${month}-${day}`;

                            if (checkinDates.includes(dateStr)) {
                                return true;
                            }

                            if (blockedDates.includes(dateStr)) {
                                return true;
                            }

                            return false;
                        }
                    ],
                    onChange: function (selectedDates) {
                        if (selectedDates.length) {
                            const nextDay = new Date(selectedDates[0].getTime());
                            nextDay.setDate(nextDay.getDate() + 1);
                            checkOutPicker.set('minDate', nextDay);
                            updateTotal();
                        }
                    },

                    onDayCreate: function (dObj, dStr, fp, dayElem) {
                        const date = dayElem.dateObj;
                        const y = date.getFullYear();
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const d = String(date.getDate()).padStart(2, '0');
                        const dateStr = `${y}-${m}-${d}`;

                        if (checkinDates.includes(dateStr) || blockedDates.includes(dateStr)) {
                            dayElem.classList.add('unavailable');
                            dayElem.title = checkinDates.includes(dateStr)
                                ? 'Check-in programado - no disponible'
                                : 'Noche ocupada - no disponible';
                        } else {
                            dayElem.classList.add('available');
                            dayElem.title = 'Disponible';
                        }
                    }

                });

                // Configurar Flatpickr para check-out
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);

                const checkOutPicker = flatpickr('#check_out', {
                    locale: 'es',
                    minDate: tomorrow,
                    dateFormat: 'Y-m-d',
                    defaultDate: '{{ old('check_out', $prefill['check_out'] ?? '') }}',
                    disable: [
                        function (date) {
                            const checkInDate = checkInPicker.selectedDates[0];
                            if (!checkInDate) return false;

                            const checkOutDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                            const current = new Date(checkInDate.getTime());
                            current.setDate(current.getDate() + 1);

                            while (current < checkOutDate) {
                                const y = current.getFullYear();
                                const m = String(current.getMonth() + 1).padStart(2, '0');
                                const d = String(current.getDate()).padStart(2, '0');
                                const nightStr = `${y}-${m}-${d}`;

                                if (blockedDates.includes(nightStr)) {
                                    return true;
                                }
                                current.setDate(current.getDate() + 1);
                            }

                            return false;
                        }
                    ],
                    onChange: function () {
                        updateTotal();
                    },
                    onDayCreate: function (dObj, dStr, fp, dayElem) {
                        const date = dayElem.dateObj;
                        const y = date.getFullYear();
                        const m = String(date.getMonth() + 1).padStart(2, '0');
                        const d = String(date.getDate()).padStart(2, '0');
                        const dateStr = `${y}-${m}-${d}`;

                        if (blockedDates.includes(dateStr)) {
                            dayElem.classList.add('unavailable');
                        } else {
                            dayElem.classList.add('available');
                        }
                    }
                });

                // Pickers listos: inicializar resumen ahora
                pickersReady = true;
                updateGuests();


                // Actualizar total
                function updateTotal() {
                    const checkIn = checkInPicker.selectedDates[0];
                    const checkOut = checkOutPicker.selectedDates[0];

                    if (checkIn && checkOut) {
                        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));

                        if (nights > 0) {
                            const formatDate = (d) => d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                            document.getElementById('summary-dates').textContent = `${formatDate(checkIn)} → ${formatDate(checkOut)}`;
                            document.getElementById('summary-nights').textContent = nights + ' noche' + (nights !== 1 ? 's' : '');

                            let totalNightsPrice = 0;
                            const current = new Date(checkIn);
                            const end = new Date(checkOut);

                            const ymd = (d) => {
                                const y = d.getFullYear();
                                const m = String(d.getMonth() + 1).padStart(2, '0');
                                const day = String(d.getDate()).padStart(2, '0');
                                return `${y}-${m}-${day}`;
                            };

                            while (current < end) {
                                const dateStr = ymd(current);
                                const nightPrice = parseFloat(rates[dateStr]) || 0;
                                totalNightsPrice += nightPrice;
                                current.setDate(current.getDate() + 1);
                            }

                            const adultsVal = document.getElementById('adults').value;
                            const childrenVal = document.getElementById('children').value;
                            const adults = parseInt(adultsVal) || 0;
                            const children = parseInt(childrenVal) || 0;
                            const guests = adults + children;
                            
                            const total = totalNightsPrice * guests;
                            
                            if (isNaN(total) || !isFinite(total)) {
                                document.getElementById('summary-total').textContent = '0.00€';
                            } else {
                                document.getElementById('summary-total').textContent = total.toFixed(2) + '€';
                            }
                        }
                    }
                }

                // Guardar datos antes de enviar (y mandar guests al back)
                document.getElementById('reservationForm').addEventListener('submit', function (e) {
                    const checkIn = checkInPicker.selectedDates[0];
                    const checkOut = checkOutPicker.selectedDates[0];
                    const adults = parseInt(document.getElementById('adults').value);
                    const children = parseInt(document.getElementById('children').value);
                    const totalGuests = adults + children;

                    if (!checkIn || !checkOut) {
                        e.preventDefault();
                        showError('Por favor, selecciona las fechas de entrada y salida');
                        return;
                    }

                    // Validación: mínimo 2 noches
                    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    if (nights < 2) {
                        e.preventDefault();
                        showError('La estancia mínima es de 2 noches.');
                        return;
                    }

                    if (totalGuests > maxCapacity) {
                        e.preventDefault();
                        showError(`La capacidad máxima es de ${maxCapacity} personas.`);
                        return;
                    }

                    hideError();

                    // rellenamos el hidden "guests" para el controlador
                    document.getElementById('guests').value = totalGuests;

                    // Si es invitado siempre enviamos al endpoint prepare (aunque la acción blade ya lo hace, reforzamos aquí)
                    @guest
                        // Asegurar acción correcta por si el HTML fue cacheado
                        this.setAttribute('action', '{{ route('reservas.prepare') }}');
                    @endguest
                });

                // Funciones para mostrar/ocultar errores
                function showError(message) {
                    const errorDiv = document.getElementById('error-message');
                    const errorText = document.getElementById('error-text');
                    errorText.textContent = message;
                    errorDiv.classList.remove('hidden');
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                function hideError() {
                    const errorDiv = document.getElementById('error-message');
                    errorDiv.classList.add('hidden');
                }

                // Auto-submit si vuelve autenticado con datos prefill y bandera $auto
                @auth
                @if(!empty($prefill) && $auto)
                (function autoSubmitAfterLogin(){
                    // Rellenar pickers si no se cargaron aún
                    const ci = '{{ $prefill['check_in'] ?? '' }}';
                    const co = '{{ $prefill['check_out'] ?? '' }}';
                    if(ci){ checkInPicker.setDate(ci, true); }
                    if(co){ checkOutPicker.setDate(co, true); }
                    updateGuests();
                    updateTotal();
                    // Validar mínima
                    const checkIn = checkInPicker.selectedDates[0];
                    const checkOut = checkOutPicker.selectedDates[0];
                    if(!checkIn || !checkOut) return; // faltan datos
                    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    if(nights < 2) return; // regla de negocio
                    // Preparar envío automático (usamos mismo form, store si auth)
                    document.getElementById('reservationForm').requestSubmit();
                })();
                @endif
                @endauth
            });

            // Función para mover el botón según el tamaño de pantalla
            function moveButtonIfMobile() {
                const isMobile = window.innerWidth <= 540;
                const form = document.getElementById('reservationForm');
                const aside = document.querySelector('.reservar-aside-mobile');
                const btnDiv = document.querySelector('.reservar-btn-mobile');
                if (!form || !aside || !btnDiv) return;
                const btnParent = btnDiv.parentNode;
                if (isMobile) {
                    // Si el botón no está después del aside, moverlo
                    if (btnParent !== aside.parentNode) {
                        // Primero, si está en el form, removerlo
                        if (btnParent === form) {
                            form.removeChild(btnDiv);
                        }
                        // Insertar después del aside
                        aside.parentNode.insertBefore(btnDiv, aside.nextSibling);
                    }
                } else {
                    // Si no es móvil, si el botón no está en el form, devolverlo
                    if (btnParent !== form) {
                        // Remover del aside.parentNode
                        aside.parentNode.removeChild(btnDiv);
                        // Añadir al final del form
                        form.appendChild(btnDiv);
                    }
                }
            }

            // Llamar al cargar
            moveButtonIfMobile();

            // Llamar al cambiar tamaño de ventana
            window.addEventListener('resize', moveButtonIfMobile);
        </script>
    </div>
@endsection
