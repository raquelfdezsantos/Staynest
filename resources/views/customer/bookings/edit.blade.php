@extends('layouts.app')

@section('title', 'Editar reserva')

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
    html[data-theme="dark"] .flatpickr-weekdays { background: var(--color-bg-card); }
    html[data-theme="dark"] span.flatpickr-weekday { color: var(--color-text-secondary); }
    html[data-theme="dark"] .flatpickr-day { color: var(--color-text-primary); }
    html[data-theme="dark"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
      background: rgba(77, 141, 148, 0.10);
      border-color: var(--color-accent);
    }
    html[data-theme="dark"] .flatpickr-day.selected,
    html[data-theme="dark"] .flatpickr-day.startRange,
    html[data-theme="dark"] .flatpickr-day.endRange {
      background: var(--color-accent);
      border-color: var(--color-accent);
      color: white;
    }
    html[data-theme="dark"] .flatpickr-day.today { border-color: var(--color-accent); }
    html[data-theme="dark"] .flatpickr-months .flatpickr-prev-month svg,
    html[data-theme="dark"] .flatpickr-months .flatpickr-next-month svg { fill: var(--color-text-primary); }

    /* Modo Claro */
    html[data-theme="light"] .flatpickr-calendar { background: #d1d1d1; border: 1px solid #e0e0e0; }
    html[data-theme="light"] .flatpickr-months { background: #d1d1d1; }
    html[data-theme="light"] .flatpickr-weekdays { background: #d1d1d1; }
    html[data-theme="light"] .flatpickr-day { color: #222; }
    html[data-theme="light"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
      background: rgba(77, 141, 148, 0.10);
      border-color: var(--color-accent);
    }
    html[data-theme="light"] .flatpickr-day.selected,
    html[data-theme="light"] .flatpickr-day.startRange,
    html[data-theme="light"] .flatpickr-day.endRange {
      background: var(--color-accent);
      border-color: var(--color-accent);
      color: white;
    }
    html[data-theme="light"] .flatpickr-day.today { border-color: var(--color-accent); }

    /* Ajustar el grid de días para el nuevo tamaño */
    .flatpickr-days { width: 308px !important; }
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

    /* Días no disponibles: fondo rojo + sin interacción (KISS) */
    .flatpickr-day.unavailable,
    .flatpickr-day.unavailable.flatpickr-disabled {
      background: rgba(239, 68, 68, 0.15) !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
    }
    </style>

  <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
    <header class="mb-8 text-center">
      <h1 class="text-4xl font-serif mb-3">Editar reserva {{ $reservation->code ?? ('#'.$reservation->id) }}</h1>
      <p class="text-neutral-300">Ajusta las fechas y huéspedes. Compara tu reserva actual con la modificada.</p>
    </header>

    <div id="error-message" class="alert alert-error mb-4 hidden">
      <strong>Revisa lo siguiente:</strong>
      <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
        <li id="error-text"></li>
      </ul>
    </div>

    <div class="grid md:grid-cols-3 gap-8" style="align-items: start;">
      <form class="md:col-span-2 space-y-6" method="POST" action="{{ route('reservas.update', $reservation) }}" id="editReservationForm">
        @csrf
        @method('PUT')

        <input type="hidden" name="guests" id="guests" value="{{ $reservation->guests }}">

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-neutral-300 mb-1">Fecha de entrada</label>
            <input type="text" id="check_in" name="check_in" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" placeholder="YYYY-MM-DD" value="{{ $reservation->check_in->format('Y-m-d') }}">
          </div>
          <div>
            <label class="block text-sm text-neutral-300 mb-1">Fecha de salida</label>
            <input type="text" id="check_out" name="check_out" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" placeholder="YYYY-MM-DD" value="{{ $reservation->check_out->format('Y-m-d') }}">
          </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-neutral-300 mb-1">Adultos</label>
            <select id="adults" name="adults" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100">
              @for($i=0;$i<=4;$i++)
                <option value="{{ $i }}" {{ (int)$reservation->adults === $i ? 'selected' : '' }}>{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="block text-sm text-neutral-300 mb-1">Niños</label>
            <select id="children" name="children" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100">
              @for($i=0;$i<=4;$i++)
                <option value="{{ $i }}" {{ (int)$reservation->children === $i ? 'selected' : '' }}>{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="block text-sm text-neutral-300 mb-1">Mascotas</label>
            <select id="pets" name="pets" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100">
              @for($i=0;$i<=2;$i++)
                <option value="{{ $i }}" {{ (int)$reservation->pets === $i ? 'selected' : '' }}>{{ $i }}</option>
              @endfor
            </select>
            <p id="pets-free-message" class="{{ (int)$reservation->pets > 0 ? '' : 'hidden' }}" style="font-size: var(--text-xs); color: var(--color-success); margin-top: 0.25rem;">¡Las mascotas se alojan gratis!</p>
          </div>
        </div>

        <div>
          <label class="block text-sm text-neutral-300 mb-1">Notas</label>
          <textarea id="notes" name="notes" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 h-28" placeholder="Cuéntanos cualquier necesidad especial">{{ old('notes', $reservation->notes) }}</textarea>
        </div>

        <div class="pt-2">
          <button type="submit" class="bg-[color:var(--color-accent)] hover:bg-[color:var(--color-accent-hover)] text-white text-sm font-semibold px-5 py-2" style="border-radius: 2px;">Guardar cambios</button>
        </div>
      </form>

      <aside class="space-y-4" style="margin-top: 3rem;">
        <div class="bg-neutral-800 border border-neutral-700 p-4" style="border-radius:var(--radius-base);">
          <h3 class="font-semibold mb-2">Reserva actual</h3>
          <ul class="text-sm text-neutral-300 space-y-1">
            <li>Fechas: <span class="text-neutral-400">{{ $reservation->check_in->format('d/m/Y') }} → {{ $reservation->check_out->format('d/m/Y') }}</span></li>
            <li>Huéspedes: <span class="text-neutral-400">
              @php
                $parts = [];
                $ad = (int) ($reservation->adults ?? 0);
                $ch = (int) ($reservation->children ?? 0);
                $pt = (int) ($reservation->pets ?? 0);
                if ($ad > 0) { $parts[] = $ad.' '.($ad === 1 ? 'adulto' : 'adultos'); }
                if ($ch > 0) { $parts[] = $ch.' '.($ch === 1 ? 'niño' : 'niños'); }
                if ($pt > 0) { $parts[] = $pt.' '.($pt === 1 ? 'mascota' : 'mascotas'); }
                echo count($parts) ? implode(', ', $parts) : ($reservation->guests . ' ' . ($reservation->guests === 1 ? 'persona' : 'personas'));
              @endphp
            </span></li>
            <li>Noches: <span class="text-neutral-400">{{ $reservation->check_in->diffInDays($reservation->check_out) }} noche{{ $reservation->check_in->diffInDays($reservation->check_out) !== 1 ? 's' : '' }}</span></li>
          </ul>
          <div class="mt-3 pt-3 border-t border-neutral-700">
            <p class="text-sm">Total</p>
            <p class="text-2xl font-serif">{{ number_format($reservation->total_price, 2, ',', '.') }} €</p>
          </div>
        </div>

        <div class="bg-neutral-800 border border-neutral-700 p-4" style="border-radius:var(--radius-base);">
          <h3 class="font-semibold mb-2">Reserva modificada</h3>
          <ul class="text-sm text-neutral-300 space-y-1">
            <li>Fechas: <span class="text-neutral-400" id="summary-dates">—</span></li>
            <li>Huéspedes: <span class="text-neutral-400" id="summary-guests">—</span></li>
            <li>Noches: <span class="text-neutral-400" id="summary-nights">—</span></li>
          </ul>
          <div class="mt-3 pt-3 border-t border-neutral-700">
            <p class="text-sm">Total estimado</p>
            <p class="text-2xl font-serif" id="summary-total">—</p>
            <p class="text-xs text-neutral-400" id="summary-diff">—</p>
          </div>
        </div>
      </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const blockedDates = @json($blockedDates ?? []);
        const checkinDates = @json($checkinDates ?? []);
        const rates = @json($rates ?? []);
        const maxCapacity = {{ (int)($maxCapacity ?? 4) }};
        const originalTotal = {{ (float) $reservation->total_price }};
        let pickersReady = false;

        // Declarar variables para los pickers
        let checkInPicker;
        let checkOutPicker;

        // Definir función updateTotal antes de usarla
        function updateTotal() {
          // Obtener fechas directamente de los inputs (KISS - Keep It Simple)
          const checkInValue = document.getElementById('check_in').value;
          const checkOutValue = document.getElementById('check_out').value;
          
          if (!checkInValue || !checkOutValue) return;
          
          const checkIn = new Date(checkInValue);
          const checkOut = new Date(checkOutValue);
          
          if (checkIn && checkOut && !isNaN(checkIn) && !isNaN(checkOut)) {
            const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            if (nights > 0) {
              const fmt = (d) => d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
              document.getElementById('summary-dates').textContent = `${fmt(checkIn)} → ${fmt(checkOut)}`;
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

              const adults = parseInt(document.getElementById('adults').value) || 0;
              const children = parseInt(document.getElementById('children').value) || 0;
              const guests = adults + children;
              const total = totalNightsPrice * guests;
              
              const totalEl = document.getElementById('summary-total');
              totalEl.textContent = total.toFixed(2).replace('.', ',') + ' €';
              totalEl.style.color = ''; // Mantener el total siempre en blanco

              const diff = total - originalTotal;
              const diffEl = document.getElementById('summary-diff');
              if (Math.abs(diff) < 0.01) {
                diffEl.textContent = 'El total se mantiene igual';
                diffEl.style.color = 'var(--color-text-secondary)';
              } else if (diff > 0) {
                diffEl.textContent = `Incremento: +${diff.toFixed(2).replace('.', ',')}€`;
                diffEl.style.color = 'var(--color-error)';
              } else {
                diffEl.textContent = `Ahorro: ${Math.abs(diff).toFixed(2).replace('.', ',')}€`;
                diffEl.style.color = 'var(--color-success)';
              }
            }
          }
        }

        function updateGuests() {
          const adults = parseInt(document.getElementById('adults').value) || 0;
          const children = parseInt(document.getElementById('children').value) || 0;
          const pets = parseInt(document.getElementById('pets').value) || 0;
          const totalGuests = adults + children;

          let text = adults + ' adulto' + (adults !== 1 ? 's' : '');
          if (children > 0) { text += ', ' + children + ' niño' + (children !== 1 ? 's' : ''); }
          if (pets > 0) { text += ', ' + pets + ' mascota' + (pets !== 1 ? 's' : ''); }
          document.getElementById('summary-guests').textContent = text;

          const petsMsg = document.getElementById('pets-free-message');
          if (petsMsg) { pets > 0 ? petsMsg.classList.remove('hidden') : petsMsg.classList.add('hidden'); }

          if (totalGuests > maxCapacity) {
            showError(`La capacidad máxima es de ${maxCapacity} personas.`);
          } else {
            hideError();
          }

          updateTotal();
        }

        document.getElementById('adults').addEventListener('change', updateGuests);
        document.getElementById('children').addEventListener('change', updateGuests);
        document.getElementById('pets').addEventListener('change', updateGuests);

        // Inicializar checkInPicker primero
        checkInPicker = flatpickr('#check_in', {
          locale: 'es',
          minDate: 'today',
          dateFormat: 'Y-m-d',
          defaultDate: '{{ $reservation->check_in->format('Y-m-d') }}',
          disable: [
            function (date) {
              const y = date.getFullYear();
              const m = String(date.getMonth() + 1).padStart(2, '0');
              const d = String(date.getDate()).padStart(2, '0');
              const dateStr = `${y}-${m}-${d}`;
              if (checkinDates.includes(dateStr)) return true;
              if (blockedDates.includes(dateStr)) return true;
              return false;
            }
          ],
          onChange: function (selectedDates) {
            if (selectedDates.length && checkOutPicker) {
              const nextDay = new Date(selectedDates[0].getTime());
              nextDay.setDate(nextDay.getDate() + 1);
              checkOutPicker.set('minDate', nextDay);
            }
            updateTotal();
          },
          onDayCreate: function (dObj, dStr, fp, dayElem) {
            const date = dayElem.dateObj;
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            const dateStr = `${y}-${m}-${d}`;
            if (checkinDates.includes(dateStr) || blockedDates.includes(dateStr)) {
              dayElem.classList.add('unavailable');
              dayElem.title = 'No disponible';
            } else {
              dayElem.classList.add('available');
              dayElem.title = 'Disponible';
            }
          }
        });

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);

        // Inicializar checkOutPicker después
        checkOutPicker = flatpickr('#check_out', {
          locale: 'es',
            minDate: tomorrow,
            dateFormat: 'Y-m-d',
            defaultDate: '{{ $reservation->check_out->format('Y-m-d') }}',
            disable: [
              function (date) {
                if (!checkInPicker || !checkInPicker.selectedDates || !checkInPicker.selectedDates[0]) return false;
                const checkInDate = checkInPicker.selectedDates[0];
                const checkOutDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
                const current = new Date(checkInDate.getTime());
                current.setDate(current.getDate() + 1);
                while (current < checkOutDate) {
                  const y = current.getFullYear();
                  const m = String(current.getMonth() + 1).padStart(2, '0');
                  const d = String(current.getDate()).padStart(2, '0');
                  const nightStr = `${y}-${m}-${d}`;
                  if (blockedDates.includes(nightStr)) return true;
                  current.setDate(current.getDate() + 1);
                }
                return false;
              }
            ],
            onChange: function () { updateTotal(); },
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

        // Inicializar el resumen con los valores actuales
        pickersReady = true;
        updateGuests();
        updateTotal();

        document.getElementById('editReservationForm').addEventListener('submit', function (e) {
          // Leer fechas directamente de los inputs (KISS)
          const checkInValue = document.getElementById('check_in').value;
          const checkOutValue = document.getElementById('check_out').value;
          const adults = parseInt(document.getElementById('adults').value) || 0;
          const children = parseInt(document.getElementById('children').value) || 0;
          const totalGuests = adults + children;

          if (!checkInValue || !checkOutValue) {
            e.preventDefault();
            showError('Por favor, selecciona las fechas de entrada y salida');
            return;
          }

          const checkIn = new Date(checkInValue);
          const checkOut = new Date(checkOutValue);
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

          document.getElementById('guests').value = totalGuests;
        });

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
      });
    </script>
  </div>
@endsection
