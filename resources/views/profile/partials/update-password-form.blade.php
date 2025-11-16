<section>
    <header style="margin-bottom: 1.5rem;">
        <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">
            Cambiar contraseña
        </h2>
        <p style="margin-top: 0.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
            Asegúrate de usar una contraseña segura para proteger tu cuenta.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
        @csrf
        @method('put')

        {{-- Campo oculto de username para accesibilidad --}}
        <input type="text" name="username" autocomplete="username" value="{{ auth()->user()->email }}" style="display: none;" aria-hidden="true">

        <div>
            <x-input-label for="update_password_current_password" value="Contraseña actual" />
            <x-text-input id="update_password_current_password" 
                          name="current_password" 
                          type="password" 
                          class="block mt-1 w-full"
                          autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <x-text-input id="update_password_password" 
                          name="password" 
                          type="password" 
                          class="block mt-1 w-full"
                          autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="update_password_password_confirmation" 
                          name="password_confirmation" 
                          type="password" 
                          class="block mt-1 w-full"
                          autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   style="font-size: var(--text-base); color: var(--color-success);">
                    Guardado.
                </p>
            @endif
        </div>
    </form>
</section>
