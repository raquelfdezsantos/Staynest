<x-guest-layout>
<div style="max-width: 60rem; margin: 0 auto;">

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

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

    <form method="POST" action="{{ route('login') }}" novalidate style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        @if(request('property'))
            <input type="hidden" name="property" value="{{ request('property') }}">
        @endif
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <div>
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus autocomplete="username" />
            </div>
            <div>
                <x-input-label for="password" value="Contraseña" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="current-password" />
            </div>
            <div class="flex items-center" style="margin-top:0.25rem;">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="sn-checkbox" style="accent-color: var(--color-accent);">
                    <span class="ms-2 text-sm text-[color:var(--color-text-secondary)]">Recuérdame</span>
                </label>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 mt-2">
            @if (Route::has('password.request'))
                <a class="text-sm transition-colors" style="color: var(--color-text-secondary); text-decoration: none;" href="{{ route('password.request') }}"
                   onmouseover="this.style.color='var(--color-accent)';"
                   onmouseout="this.style.color='var(--color-text-secondary)';">
                    ¿Has olvidado tu contraseña?
                </a>
            @endif
            <div class="flex items-center gap-3">
                <a href="{{ route('register') }}{{ request('property') ? '?property=' . request('property') : '' }}" class="btn-action btn-action-secondary sn-sentence">
                    Registrarse
                </a>
                <x-primary-button class="sn-sentence py-2 px-6">Iniciar sesión</x-primary-button>
            </div>
        </div>
    </form>

    <style>
    /* Checkbox login igual a admin */
    .sn-checkbox {
        width: 1rem;
        height: 1rem;
        border-radius: 2px;
        border: 1px solid var(--color-border-light);
        background: var(--color-bg-elevated);
        cursor: pointer;
        accent-color: var(--color-accent);
    }
    .sn-checkbox:checked {
        background-color: var(--color-accent) !important;
        border-color: var(--color-accent) !important;
    }
    .sn-checkbox:hover { border-color: var(--color-accent); }
    .sn-checkbox:focus { outline: none; box-shadow: none; border-color: var(--color-accent); }
    </style>

    </div>
</x-guest-layout>
