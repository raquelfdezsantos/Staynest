<x-guest-layout>
    <form method="POST" action="{{ route('register.client') }}" novalidate>
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" value="Nombre completo *" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
        </div>

        <!-- Correo electrónico -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo electrónico *" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña *" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
        </div>

        <!-- Teléfono -->
        <div class="mt-4">
            <x-input-label for="phone" value="Teléfono" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="+34 600 000 000" />
            <p class="text-xs text-neutral-400 mt-1">Opcional. Se solicitará si vas a realizar un pago.</p>
        </div>

        <!-- Fecha de nacimiento -->
        <div class="mt-4">
            <x-input-label for="birth_date" value="Fecha de nacimiento" />
            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" />
            <p class="text-xs text-neutral-400 mt-1">Opcional. Debes ser mayor de 18 años para reservar.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error mt-4">
                <strong>Revisa los siguientes campos:</strong>
                <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('register') }}" class="text-sm" style="color: var(--color-text-secondary);" onmouseover="this.style.color='var(--color-accent)';" onmouseout="this.style.color='var(--color-text-secondary);';">
                ← Volver
            </a>

            <x-primary-button>
                Crear cuenta
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>