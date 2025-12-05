<x-guest-layout>
    <div>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Calendario Flatpickr - Modo Oscuro */
            html[data-theme="dark"] .flatpickr-calendar {
            background: rgb(38, 38, 38);
            border: 1px solid var(--color-border-light);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        html[data-theme="dark"] .flatpickr-months {
            background: rgb(38, 38, 38);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months,
        html[data-theme="dark"] .flatpickr-current-month input.cur-year {
            background: rgb(38, 38, 38);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-weekdays {
            background: rgb(38, 38, 38);
        }

        html[data-theme="dark"] span.flatpickr-weekday {
            color: var(--color-text-secondary);
        }

        html[data-theme="dark"] .flatpickr-day {
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-day:hover {
            background: rgba(77, 141, 148, 0.2);
            color: var(--color-text-primary);
        }

        html[data-theme="dark"] .flatpickr-day.selected {
            background: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }

        html[data-theme="dark"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }

        html[data-theme="dark"] .flatpickr-months .flatpickr-prev-month svg,
        html[data-theme="dark"] .flatpickr-months .flatpickr-next-month svg {
            fill: var(--color-text-primary);
        }

        html[data-theme="light"] .flatpickr-calendar {
            background: white;
            border: 1px solid #e0e0e0;
        }

        html[data-theme="light"] .flatpickr-months {
            background: white;
        }

        html[data-theme="light"] .flatpickr-weekdays {
            background: white;
        }

        html[data-theme="light"] .flatpickr-day.selected {
            background: var(--color-accent);
            color: white;
        }

        html[data-theme="light"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }

        .flatpickr-days {
            width: 308px !important;
        }
        
        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: center !important;
        }
        
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
        
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            opacity: 0.4 !important;
        }
        </style>
        
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-serif mb-4">Crear cuenta de Huésped</h1>
        <p class="text-neutral-300">Completa el formulario para registrarte como huésped.</p>
    </header>

    @if ($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <strong>Revisa los siguientes campos:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.client') }}" style="margin-top: 1.5rem;" novalidate>
        @csrf
        
        @if(request('property'))
            <input type="hidden" name="property" value="{{ request('property') }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="name" value="Nombre completo *" />
                <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div>
                <x-input-label for="email" value="Correo electrónico *" />
                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required autocomplete="username" />
            </div>

            <div>
                <x-input-label for="password" value="Contraseña *" />
                <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
            </div>

            <div>
                <x-input-label for="phone" value="Teléfono" />
                <x-text-input id="phone" name="phone" type="tel" class="block mt-1 w-full" :value="old('phone')" placeholder="+34 600 000 000" autocomplete="tel" />
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Opcional. Se solicitará si vas a realizar un pago.</p>
            </div>

            <div>
                <x-input-label for="birth_date" value="Fecha de nacimiento *" />
                <x-text-input id="birth_date" name="birth_date" type="text" class="block mt-1 w-full" :value="old('birth_date')" required placeholder="Selecciona tu fecha de nacimiento" autocomplete="bday" />
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Debes ser mayor de 18 años para registrarte.</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 0.5rem;">
            <a href="{{ route('register') }}" class="btn-action btn-action-secondary sn-sentence" style="height:36px; display:inline-flex; align-items:center; padding:0 1.25rem; line-height:34px;">
                Volver
            </a>
            <x-primary-button class="sn-sentence py-2 px-5">Crear cuenta</x-primary-button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
            
            flatpickr('#birth_date', {
                locale: 'es',
                dateFormat: 'Y-m-d',
                maxDate: maxDate,
                defaultDate: '{{ old('birth_date') }}',
                allowInput: true
            });
        });
    </script>
    </div>
</x-guest-layout>