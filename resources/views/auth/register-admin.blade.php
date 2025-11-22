@extends('layouts.app')

@section('title', 'Registro de Administrador')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3">Registro de Administrador</h1>
            <p class="text-neutral-300">
                Completa todos los datos para crear tu cuenta y alojamiento
            </p>
        </header>

        <form method="POST" action="{{ route('register.admin') }}" novalidate>
        @csrf

        @if ($errors->any())
            <div class="alert alert-error mb-6">
                <strong>Revisa los siguientes campos:</strong>
                <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Datos Personales -->
        <div class="mb-16">
            <h3 class="text-xl font-semibold mb-4">Datos Personales</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre -->
                <div>
                    <x-input-label for="name" value="Nombre completo *" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>

                <!-- Correo electrónico -->
                <div>
                    <x-input-label for="email" value="Correo electrónico *" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>

                <!-- Contraseña -->
                <div>
                    <x-input-label for="password" value="Contraseña *" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" />
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation"
                                    required autocomplete="new-password" />
                </div>

                <!-- Teléfono -->
                <div>
                    <x-input-label for="phone" value="Teléfono *" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="+34 600 000 000" autocomplete="tel" />
                </div>

                <!-- Fecha de nacimiento -->
                <div>
                    <x-input-label for="birth_date" value="Fecha de nacimiento *" />
                    <x-text-input id="birth_date" class="block mt-1 w-full" type="text" name="birth_date" :value="old('birth_date')" required placeholder="Selecciona tu fecha de nacimiento" autocomplete="bday" />
                    <p class="text-xs text-neutral-400 mt-1">Debes ser mayor de 18 años.</p>
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Dirección completa *" />
                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required autocomplete="street-address" />
                </div>

                <!-- DNI -->
                <div>
                    <x-input-label for="document_id" value="DNI/NIF *" />
                    <x-text-input id="document_id" class="block mt-1 w-full" type="text" name="document_id" :value="old('document_id')" required placeholder="12345678A" />
                </div>

                <!-- Método de cobro -->
                <div>
                    <x-input-label for="payment_method" value="Método de cobro *" />
                    <select id="payment_method" name="payment_method" class="sn-input block mt-1 w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100" required>
                        <option value="">Seleccionar...</option>
                        <option value="stripe" {{ old('payment_method') === 'stripe' ? 'selected' : '' }}>Stripe (recomendado)</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Transferencia bancaria</option>
                        <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Datos del Alojamiento -->
        <div class="mb-7">
            <h3 class="text-xl font-semibold mb-4">Datos del Alojamiento</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nombre del alojamiento -->
                <div class="md:col-span-2">
                    <x-input-label for="property_name" value="Nombre del alojamiento *" />
                    <x-text-input id="property_name" class="block mt-1 w-full" type="text" name="property_name" :value="old('property_name')" required />
                </div>

                <!-- Dirección del alojamiento -->
                <div class="md:col-span-2">
                    <x-input-label for="property_address" value="Dirección del alojamiento *" />
                    <x-text-input id="property_address" class="block mt-1 w-full" type="text" name="property_address" :value="old('property_address')" required />
                </div>

                <!-- Ciudad -->
                <div>
                    <x-input-label for="city" value="Ciudad *" />
                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
                </div>

                <!-- Código Postal -->
                <div>
                    <x-input-label for="postal_code" value="Código Postal *" />
                    <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" required />
                </div>

                <!-- Provincia -->
                <div>
                    <x-input-label for="province" value="Provincia *" />
                    <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')" required />
                </div>

                <!-- Capacidad -->
                <div>
                    <x-input-label for="capacity" value="Capacidad (huéspedes) *" />
                    <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="old('capacity', 4)" required min="1" max="50" />
                </div>

                <!-- Licencia turística -->
                <div>
                    <x-input-label for="tourism_license" value="Nº Licencia Turística *" />
                    <x-text-input id="tourism_license" class="block mt-1 w-full" type="text" name="tourism_license" :value="old('tourism_license')" required placeholder="VT-28-0001234" />
                </div>

                <!-- Registro de alquiler -->
                <div>
                    <x-input-label for="rental_registration" value="Nº Registro de Alquiler *" />
                    <x-text-input id="rental_registration" class="block mt-1 w-full" type="text" name="rental_registration" :value="old('rental_registration')" required placeholder="ATR-28-001234-2024" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 register-actions">
            <a href="{{ route('register') }}" class="btn-action btn-action-secondary">Volver</a>

            <x-primary-button>
                Crear cuenta
            </x-primary-button>
        </div>
    </form>

    <style>
        .register-actions .btn-action {
            text-transform: none !important;
            letter-spacing: 0;
            font-weight: 600;
            font-size: var(--text-sm);
            line-height: 1.25rem;
            padding: 0.5rem 1.25rem;
            border-radius: 2px;
            color: #fff;
        }
        .register-actions .btn-action-secondary {
            background: transparent;
            border: 1px solid var(--color-accent) !important;
        }
        .register-actions .btn-action-secondary:hover {
            color: var(--color-accent) !important;
            background-color: rgba(77, 141, 148, 0.10) !important;
            border-color: transparent !important;
        }
        html[data-theme="light"] .register-actions .btn-action-secondary {
            color: #000 !important;
        }

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
        
        html[data-theme="dark"] .flatpickr-day:hover {
            background: rgba(77,141,148,0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="dark"] .flatpickr-day.selected {
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
        
        html[data-theme="light"] .flatpickr-day:hover {
            background: rgba(77,141,148,0.10);
            border-color: var(--color-accent);
        }
        
        html[data-theme="light"] .flatpickr-day.selected {
            background: var(--color-accent);
            border-color: var(--color-accent);
            color: white;
        }
        
        html[data-theme="light"] .flatpickr-day.today {
            border-color: var(--color-accent);
        }
        
        /* Ajustar el grid de días */
        .flatpickr-days {
            width: 308px !important;
        }
        
        .dayContainer {
            width: 308px !important;
            min-width: 308px !important;
            max-width: 308px !important;
            justify-content: center !important;
        }
        
        /* Hacer los días cuadrados */
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Calcular fecha máxima (18 años atrás desde hoy)
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
@endsection