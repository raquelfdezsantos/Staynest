@extends('layouts.app')

@section('title', 'Registro de Administrador')

@section('content')
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
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>

                <!-- Correo electrónico -->
                <div>
                    <x-input-label for="email" value="Correo electrónico *" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
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
                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="+34 600 000 000" />
                </div>

                <!-- Fecha de nacimiento -->
                <div>
                    <x-input-label for="birth_date" value="Fecha de nacimiento *" />
                    <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
                    <p class="text-xs text-neutral-400 mt-1">Debes ser mayor de 18 años.</p>
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <x-input-label for="address" value="Dirección completa *" />
                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
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

        <div class="flex items-center justify-between pt-3">
            <a href="{{ route('register') }}" class="text-sm" style="color: var(--color-text-secondary);" onmouseover="this.style.color='var(--color-accent)';" onmouseout="this.style.color='var(--color-text-secondary);';">
                ← Volver
            </a>

            <x-primary-button>
                Crear cuenta
            </x-primary-button>
        </div>
    </form>
    </div>
@endsection