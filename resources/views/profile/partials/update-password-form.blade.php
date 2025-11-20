    @if (session('status') === 'password-updated')
        <x-alert type="success" class="mb-4">Contraseña actualizada.</x-alert>
    @endif

    {{-- Mostrar errores todos juntos arriba --}}
    @if ($errors->updatePassword->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <strong>Revisa los siguientes campos:</strong>
            <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                @foreach ($errors->updatePassword->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <x-text-input id="update_password_password" 
                          name="password" 
                          type="password" 
                          class="block mt-1 w-full"
                          autocomplete="new-password" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="update_password_password_confirmation" 
                          name="password_confirmation" 
                          type="password" 
                          class="block mt-1 w-full"
                          autocomplete="new-password" />
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <x-primary-button class="sn-sentence py-2 px-5">Guardar</x-primary-button>
        </div>
    </form>
