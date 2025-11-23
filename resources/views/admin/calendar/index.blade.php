<x-app-layout>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <div class="sn-reservar max-w-6xl mx-auto px-4 py-10 admin-slim-badges">
    
    {{-- Header centrado como en Dashboard --}}
    <header class="mb-16 text-center">
      <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Calendario y precios</h1>
      <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Gestiona los precios por noche, bloquea fechas no disponibles y controla tu calendario.</p>
    </header>

    {{-- Mensajes --}}
    @if (session('success'))
      <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-error mb-6">{{ session('error') }}</div>
    @endif

    {{-- Grid de 3 columnas --}}
    <div class="calendar-grid">
      {{-- Columna 1: Gestión de Precios --}}
      <div class="calendar-card">
        <div class="calendar-card-header">
          <h3 class="calendar-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Precios
          </h3>
        </div>
        <div class="calendar-card-body">
          <form method="POST" action="{{ route('admin.property.calendar.set-price', $property->slug) }}" class="space-y-4">
            @csrf
            <x-validation-errors class="mb-4" />
            
            @if($property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <div class="calendar-info-box" style="margin-bottom: 1rem;">
              <strong>Alojamiento:</strong> {{ $property->name }}
            </div>
            @endif

            <div>
              <label for="price" class="calendar-label">Precio por noche (€)</label>
              <input type="number" name="price" id="price" step="0.01" min="0" class="calendar-input" placeholder="ej. 85.00" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label for="start_price" class="calendar-label">Desde</label>
                <input type="date" name="start" id="start_price" class="calendar-input" required>
              </div>
              <div>
                <label for="end_price" class="calendar-label">Hasta</label>
                <input type="date" name="end" id="end_price" class="calendar-input" required>
              </div>
            </div>

            <button type="submit" class="calendar-btn calendar-btn-primary">
              Establecer precio
            </button>
          </form>
        </div>
      </div>

      {{-- Columna 2: Bloquear fechas --}}
      <div class="calendar-card">
        <div class="calendar-card-header">
          <h3 class="calendar-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;"><rect x="5" y="11" width="14" height="10" rx="2" ry="2"/><path d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
            Bloquear
          </h3>
        </div>
        <div class="calendar-card-body">
          <form method="POST" action="{{ route('admin.property.calendar.block', $property->slug) }}" class="space-y-4">
            @csrf
            <x-validation-errors class="mb-4" />
            
            @if($property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <div class="calendar-info-box" style="margin-bottom: 1.5rem;">
              <strong>Alojamiento:</strong> {{ $property->name }}
            </div>
            @endif
            
            <div style="margin-top: 1rem;">
              <div class="calendar-info-box">
                Las noches bloqueadas no estarán disponibles para reservar.
              </div>

              <div class="grid grid-cols-2 gap-3" style="margin-top: 1rem;">
                <div>
                  <label for="start_block" class="calendar-label">Desde</label>
                  <input type="date" name="start" id="start_block" class="calendar-input" required>
                </div>
                <div>
                  <label for="end_block" class="calendar-label">Hasta</label>
                  <input type="date" name="end" id="end_block" class="calendar-input" required>
                </div>
              </div>

              <button type="submit" class="calendar-btn calendar-btn-danger" style="margin-top: 1rem;">
                Bloquear noches
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Columna 3: Desbloquear fechas --}}
      <div class="calendar-card">
        <div class="calendar-card-header">
          <h3 class="calendar-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;"><rect x="5" y="11" width="14" height="10" rx="2" ry="2"/><path d="M12 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/><path d="M8 11V7a4 4 0 0 1 4 0 4 4 0 0 1 4 0v4"/></svg>
            Desbloquear
          </h3>
        </div>
        <div class="calendar-card-body">
          <form method="POST" action="{{ route('admin.property.calendar.unblock', $property->slug) }}" class="space-y-4">
            @csrf
            
            @if($property)
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <div class="calendar-info-box" style="margin-bottom: 1.5rem;">
              <strong>Alojamiento:</strong> {{ $property->name }}
            </div>
            @endif
            
            <div style="margin-top: 1rem;">
              <div class="calendar-info-box">
                Desbloquea fechas previamente bloqueadas.
              </div>

              <div class="grid grid-cols-2 gap-3" style="margin-top: 1rem;">
                <div>
                  <label for="start_unblock" class="calendar-label">Desde</label>
                  <input type="date" name="start" id="start_unblock" class="calendar-input" required>
                </div>
                <div>
                  <label for="end_unblock" class="calendar-label">Hasta</label>
                  <input type="date" name="end" id="end_unblock" class="calendar-input" required>
                </div>
              </div>

              <button type="submit" class="calendar-btn calendar-btn-success" style="margin-top: 1rem;">
                Desbloquear noches
              </button>
            </div>
          </form>
        </div>
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
    
    /* Hover sólo en días disponibles (KISS) */
    html[data-theme="dark"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
      background: rgba(77, 141, 148, 0.10);
      border-color: var(--color-accent);
    }
    /* Días bloqueados: sin hover y sin interacción */
    .flatpickr-day.unavailable {
      background: rgba(239, 68, 68, 0.15) !important;
      cursor: not-allowed !important;
      pointer-events: none !important; /* elimina hover y clic */
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
    
    html[data-theme="light"] .flatpickr-day:hover:not(.flatpickr-disabled):not(.unavailable) {
      background: rgba(77, 141, 148, 0.10);
      border-color: var(--color-accent);
    }
    html[data-theme="light"] .flatpickr-day.unavailable {
      background: rgba(239, 68, 68, 0.15) !important;
      cursor: not-allowed !important;
      pointer-events: none !important;
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

    /* === Admin Calendar Page Styling === */
    .admin-calendar-page {
      font-family: var(--font-sans);
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .calendar-card {
      background: var(--color-bg-card);
      border: 1px solid var(--color-border-light);
      border-radius: 2px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .calendar-card-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--color-border-light);
      background: rgba(255, 255, 255, 0.02);
    }

    .calendar-card-body {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .calendar-card-body form {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .calendar-card-body form .space-y-4 {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .calendar-card-body form .space-y-4 > :last-child {
      margin-top: auto;
    }

    .calendar-section-title {
      font-size: var(--text-base);
      font-weight: 700;
      color: var(--color-text-primary);
      margin: 0;
      display: flex;
      align-items: center;
    }

    .calendar-label {
      display: block;
      font-size: var(--text-sm);
      font-weight: 600;
      color: var(--color-text-secondary);
      margin-bottom: 0.5rem;
    }

    .calendar-input {
      width: 100%;
      background-color: var(--color-bg-secondary);
      border: 1px solid var(--color-border-light);
      border-radius: 2px;
      padding: 0.625rem 0.875rem;
      color: var(--color-text-primary);
      font-size: var(--text-sm);
      font-family: var(--font-sans);
      transition: border-color 0.2s ease;
    }

    .calendar-input:focus {
      outline: none;
      border-color: var(--color-accent);
    }

    .calendar-input::placeholder {
      color: var(--color-text-tertiary);
    }

    .calendar-info-box {
      background: rgba(77, 141, 148, 0.08);
      border: 1px solid rgba(77, 141, 148, 0.2);
      border-radius: 2px;
      padding: 0.75rem;
      font-size: var(--text-sm);
      color: var(--color-text-secondary);
      line-height: 1.5;
    }

    .calendar-btn {
      width: 100%;
      padding: 0.625rem 1.25rem;
      border-radius: 2px;
      border: none;
      font-size: var(--text-sm);
      font-weight: 600;
      font-family: var(--font-sans);
      cursor: pointer;
      transition: background-color var(--transition-fast);
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .calendar-btn-primary {
      background-color: var(--color-accent);
      color: #fff;
    }

    .calendar-btn-primary:hover {
      background-color: var(--color-accent-hover);
    }

    .calendar-btn-danger {
      background-color: var(--color-btn-error);
      color: #fff;
    }

    .calendar-btn-danger:hover {
      background-color: #B84E4B;
    }

    .calendar-btn-success {
      background-color: var(--color-btn-success);
      color: #fff;
    }

    .calendar-btn-success:hover {
      background-color: #5F7A67;
    }

    /* Light mode overrides */
    html[data-theme="light"] .calendar-card {
      background-color: #fff;
    }

    html[data-theme="light"] .calendar-card-header {
      background: rgba(0, 0, 0, 0.02);
    }

    html[data-theme="light"] .calendar-input {
      background-color: #f9fafb;
    }

    html[data-theme="light"] .calendar-info-box {
      background: rgba(77, 141, 148, 0.06);
      border-color: rgba(77, 141, 148, 0.15);
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const blockedDates = @json($blockedDates ?? []);
      
      // Inputs para precios y bloquear (SÍ deshabilitar fechas bloqueadas)
      const blockInputs = document.querySelectorAll('#start_price, #end_price, #start_block, #end_block');
      blockInputs.forEach(input => {
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
            
            // Marcar y deshabilitar fechas bloqueadas
            if (blockedDates.includes(dateStr)) {
              dayElem.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
              dayElem.style.cursor = 'not-allowed';
              dayElem.title = 'Noche bloqueada u ocupada';
              dayElem.classList.add('unavailable');
            }
          }
        });
      });

      // Inputs para desbloquear (PERMITIR seleccionar fechas bloqueadas)
      const unblockInputs = document.querySelectorAll('#start_unblock, #end_unblock');
      unblockInputs.forEach(input => {
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
            
            // Solo marcar visualmente (sin deshabilitar)
            if (blockedDates.includes(dateStr)) {
              dayElem.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
              dayElem.title = 'Noche bloqueada - haz clic para desbloquear';
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
            const property = properties.find(p => p.id == propertyId);
            if (property) {
                window.location.href = '/propiedades/' + property.slug + '/admin/calendar';
            }
          }
        });
      });
    });
  </script>
</x-app-layout>
