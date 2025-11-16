<section>
    <header style="margin-bottom: 1.5rem;">
        <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">
            Información personal
        </h2>
        <p style="margin-top: 0.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
            Actualiza tu nombre, email y foto de perfil.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Nombre --}}
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" 
                          name="name" 
                          type="text" 
                          class="block mt-1 w-full"
                          :value="old('name', $user->name)"
                          required 
                          autofocus 
                          autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" 
                          name="email" 
                          type="email" 
                          class="block mt-1 w-full"
                          :value="old('email', $user->email)" 
                          required 
                          autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div style="margin-top: 0.75rem;">
                    <p style="font-size: var(--text-base); color: var(--color-text-secondary);">
                        Tu dirección de email no está verificada.
                        <button form="send-verification"
                                style="text-decoration: underline; color: var(--color-accent); background: none; border: none; cursor: pointer; padding: 0; font-size: var(--text-base);">
                            Haz clic aquí para reenviar el email de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top: 0.5rem; font-size: var(--text-base); font-weight: 500; color: var(--color-success);">
                            Se ha enviado un nuevo enlace de verificación a tu email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" value="Foto de perfil" />
            <input id="avatar" 
                   name="avatar" 
                   type="file" 
                   accept="image/jpeg,image/png,image/webp"
                   class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] mt-1"
                   onchange="previewAvatar(event)">
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            <p style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.5rem;">
                Formatos: JPG, PNG, WEBP. Máx. 2MB.
            </p>

            <div style="display: flex; align-items: center; margin-top: 1rem;">
                <div id="avatar-preview-container" 
                     style="height: 5rem; width: 5rem; border-radius: 50%; overflow: hidden; border: 1px solid var(--color-border-light); display: flex; align-items: center; justify-content: center; background: var(--color-bg-card);">
                    <img id="avatar-preview" 
                         src="{{ $user->avatar_path ? Storage::disk('public')->url($user->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128' }}" 
                         style="height: 5rem; width: 5rem; object-fit: cover;" 
                         alt="Avatar">
                </div>
            </div>
        </div>

        <script>
        function previewAvatar(event) {
            const input = event.target;
            const preview = document.getElementById('avatar-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <x-primary-button>Guardar cambios</x-primary-button>

            @if (session('status') === 'profile-updated')
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