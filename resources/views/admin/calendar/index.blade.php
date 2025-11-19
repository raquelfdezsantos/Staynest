<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl leading-tight" style="font-family: var(--font-sans); color: var(--color-text-primary);">Calendario (bloquear/desbloquear)</h2>
  </x-slot>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <div class="admin-calendar-page max-w-4xl mx-auto px-4 py-12">
    {{-- Mensajes --}}
    @if (session('success'))
      <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-error mb-6">{{ session('error') }}</div>
    @endif

    {{-- Formulario de bloqueo --}}
    <div class="calendar-card mb-8">
      <div class="p-6">
        <form method="POST" action="{{ route('admin.calendar.block') }}" class="space-y-4">
          @csrf
          <x-validation-errors class="mb-4" />
          
          <h3 class="calendar-section-title">Bloquear noches</h3>
          
          <div class="calendar-info-box">
            <strong>Ejemplo:</strong> Para bloquear las noches del 14, 15 y 16 de noviembre:<br>
            → Desde: <code class="calendar-code">2025-11-14</code> | Hasta: <code class="calendar-code">2025-11-16</code><br>
            <span class="text-xs">Esto bloqueará esas 3 noches. Nadie podrá hacer check-in el 14, 15 o 16. El primer check-in disponible sería el 17.</span>
          </div>

          <div>
            <x-input-label for="property_block" value="Alojamiento" />
            <select name="property_id" id="property_block" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]">
              @foreach(\App\Models\Property::select(['id','name'])->get() as $p)
                <option value="{{ $p->id }}" {{ (isset($selectedPropertyId) && $selectedPropertyId == $p->id) ? 'selected' : '' }}>
                  {{ $p->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <x-input-label for="start_block" value="Desde (primera noche bloqueada)" />
              <input type="date" name="start" id="start_block" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]" required>
            </div>
            <div>
              <x-input-label for="end_block" value="Hasta (última noche bloqueada, INCLUSIVO)" />
              <input type="date" name="end" id="end_block" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]" required>
            </div>
          </div>

          <div style="align-self:flex-start;">
            <x-primary-button>Bloquear</x-primary-button>
          </div>
        </form>
      </div>
    </div>

    {{-- Formulario de desbloqueo --}}
    <div class="calendar-card">
      <div class="p-6">
        <form method="POST" action="{{ route('admin.calendar.unblock') }}" class="space-y-4">
          @csrf
          
          <h3 class="calendar-section-title">Desbloquear noches</h3>

          <div>
            <x-input-label for="property_unblock" value="Alojamiento" />
            <select name="property_id" id="property_unblock" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]">
              @foreach(App\Models\Property::select(['id','name'])->get() as $p)
                <option value="{{ $p->id }}" {{ (isset($selectedPropertyId) && $selectedPropertyId == $p->id) ? 'selected' : '' }}>
                  {{ $p->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <x-input-label for="start_unblock" value="Desde (primera noche desbloqueada)" />
              <input type="date" name="start" id="start_unblock" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]" required>
            </div>
            <div>
              <x-input-label for="end_unblock" value="Hasta (última noche desbloqueada, INCLUSIVO)" />
              <input type="date" name="end" id="end_unblock" class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)]" required>
            </div>
          </div>

          <div style="align-self:flex-start;">
            <x-primary-button>Desbloquear</x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
    
    html[data-theme="dark"] .flatpickr-day:hover:not(.flatpickr-disabled) {
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
    
    html[data-theme="light"] .flatpickr-day:hover:not(.flatpickr-disabled) {
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
    
    /* Hacer los círculos más pequeños con espacio entre ellos */
    .flatpickr-day {
      max-width: 38px !important;
      max-height: 38px !important;
      width: 38px !important;
      height: 38px !important;
      line-height: 38px !important;
      margin: 2px !important;
    }
    
    /* Días de meses anterior/posterior más apagados */
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
      opacity: 0.4 !important;
    }

    /* === Admin Calendar Page Styling === */
    .admin-calendar-page {
      font-family: var(--font-sans);
    }

    .calendar-card {
      background: var(--color-bg-card);
      border: 1px solid var(--color-border-light);
      border-radius: 2px;
      overflow: hidden;
    }

    .calendar-section-title {
      font-size: var(--text-lg);
      font-weight: 600;
      color: var(--color-text-primary);
      margin-bottom: 1rem;
    }

    .calendar-info-box {
      background: rgba(77, 141, 148, 0.1);
      border: 1px solid rgba(77, 141, 148, 0.3);
      border-radius: 2px;
      padding: 0.75rem;
      font-size: var(--text-sm);
      color: var(--color-text-secondary);
      line-height: 1.6;
    }

    .calendar-code {
      background: rgba(0, 0, 0, 0.2);
      padding: 2px 6px;
      border-radius: 2px;
      font-family: monospace;
      font-size: 0.9em;
      color: var(--color-accent);
    }

    /* Light mode overrides */
    html[data-theme="light"] .admin-calendar-page .calendar-card {
      background-color: #fff;
    }

    html[data-theme="light"] .admin-calendar-page .calendar-info-box {
      background: rgba(77, 141, 148, 0.08);
      border-color: rgba(77, 141, 148, 0.25);
    }

    html[data-theme="light"] .admin-calendar-page .calendar-code {
      background: rgba(0, 0, 0, 0.05);
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const blockedDates = @json($blockedDates ?? []);
      
      const dateInputs = document.querySelectorAll('input[type="date"]');
      
      dateInputs.forEach(input => {
        flatpickr(input, {
          minDate: 'today',
          dateFormat: 'Y-m-d',
          locale: 'es',
          disableMobile: true,
          onDayCreate: function(dObj, dStr, fp, dayElem) {
            const date = dayElem.dateObj;
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;
            
            // Marcar fechas bloqueadas/ocupadas (borde rojo como en Reservar)
            if (blockedDates.includes(dateStr)) {
              dayElem.style.backgroundColor = 'rgba(255, 0, 0, 0.05)';
              dayElem.style.border = '1px solid var(--color-error)';
              dayElem.style.cursor = 'not-allowed';
              dayElem.title = 'Noche bloqueada u ocupada';
            }
          }
        });
      });

      // Actualizar calendario cuando cambie la propiedad
      const propertySelects = document.querySelectorAll('select[name="property_id"]');
      propertySelects.forEach(select => {
        select.addEventListener('change', function() {
          const propertyId = this.value;
          if (propertyId) {
            window.location.href = '{{ route("admin.calendar.index") }}?property_id=' + propertyId;
          }
        });
      });
    });
  </script>
</x-app-layout>
