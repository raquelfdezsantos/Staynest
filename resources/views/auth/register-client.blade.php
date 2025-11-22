<x-guest-layout>
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
                <x-input-label for="birth_date" value="Fecha de nacimiento" />
                <x-text-input id="birth_date" name="birth_date" type="date" class="block mt-1 w-full" :value="old('birth_date')" autocomplete="bday" />
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Opcional. Debes ser mayor de 18 años para reservar.</p>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 0.5rem;">
            <a href="{{ route('register') }}" class="btn-action btn-action-secondary sn-sentence">
                ← Volver
            </a>
            <x-primary-button class="sn-sentence py-2 px-5">Crear cuenta</x-primary-button>
        </div>
    </form>
</x-guest-layout>