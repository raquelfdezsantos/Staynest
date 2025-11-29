    @if (session('status') === 'profile-updated')
        <x-alert type="success" class="mb-4">Perfil actualizado.</x-alert>
    @endif

    {{-- Mostrar errores todos juntos arriba --}}
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

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;" enctype="multipart/form-data">
        @csrf
        @method('patch')        {{-- Nombre --}}
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" 
                          name="name" 
                          type="text" 
                          class="block mt-1 w-full"
                          :value="old('name', $user->name)"
                          autofocus 
                          autocomplete="name" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" 
                          name="email" 
                          type="text" 
                          class="block mt-1 w-full"
                          :value="old('email', $user->email)" 
                          autocomplete="username" />

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
                        <x-alert type="success" class="mt-2">Se ha enviado un nuevo enlace de verificación a tu email.</x-alert>
                    @endif
                </div>
            @endif
        </div>

        {{-- Teléfono --}}
        <div>
            <x-input-label for="phone" value="Teléfono" />
            <x-text-input id="phone"
                          name="phone"
                          type="tel"
                          class="block mt-1 w-full"
                          :value="old('phone', $user->phone)"
                          autocomplete="tel"
                          placeholder="+34 600 000 000" />
            @if($user->role === 'customer')
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Se solicitará si vas a realizar un pago.</p>
            @endif
        </div>

        {{-- Fecha de nacimiento --}}
        <div>
            <x-input-label for="birth_date" value="Fecha de nacimiento" />
            <x-text-input id="birth_date"
                          name="birth_date"
                          type="text"
                          class="block mt-1 w-full"
                          :value="old('birth_date', $user->birth_date)"
                          placeholder="Selecciona tu fecha de nacimiento"
                          autocomplete="bday" />
            @if($user->role === 'customer')
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Debes ser mayor de 18 años.</p>
            @endif
        </div>

        {{-- Dirección fiscal --}}
        <div>
            <x-input-label for="address" value="Dirección (facturación)" />
            <x-text-input id="address"
                          name="address"
                          type="text"
                          class="block mt-1 w-full"
                          :value="old('address', $user->address)"
                          autocomplete="street-address" />
            <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Incluye calle, número, localidad y código postal para la factura.</p>
        </div>

        {{-- DNI/NIF --}}
        <div>
            <x-input-label for="document_id" :value="$user->role === 'admin' ? 'DNI/NIF' : 'NIF/CIF/PAS/Otro'" />
            <x-text-input id="document_id"
                          name="document_id"
                          type="text"
                          class="block mt-1 w-full"
                          :value="old('document_id', $user->document_id)"
                          autocomplete="off" />
            <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Necesario para facturación y completar el pago.</p>
        </div>

        @if($user->role === 'admin')
            {{-- Método de cobro (solo admin) --}}
            <div>
                <x-input-label for="payment_method" value="Método de cobro" />
                <select id="payment_method" name="payment_method" class="sn-input block mt-1 w-full" style="background-color: var(--color-bg-secondary); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); padding: 0.5rem 0.75rem; color: var(--color-text-primary);">
                    <option value="">Seleccionar...</option>
                    <option value="stripe" {{ old('payment_method', $user->payment_method) === 'stripe' ? 'selected' : '' }}>Stripe (recomendado)</option>
                    <option value="bank_transfer" {{ old('payment_method', $user->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Transferencia bancaria</option>
                    <option value="paypal" {{ old('payment_method', $user->payment_method) === 'paypal' ? 'selected' : '' }}>PayPal</option>
                </select>
                <p style="font-size: var(--text-xs); color: var(--color-text-secondary); margin-top:0.25rem;">Método que usarás para recibir los pagos de tus reservas.</p>
            </div>
        @endif

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" value="Foto de perfil" />
            
            {{-- Input file oculto --}}
            <input id="avatar" 
                   name="avatar" 
                   type="file" 
                   accept="image/jpeg,image/png,image/webp"
                   style="display: none;"
                   onchange="previewAvatar(event)">
            
            {{-- Botón custom para activar el input --}}
            <button type="button" 
                    onclick="document.getElementById('avatar').click()"
                    class="btn-action btn-action-secondary sn-sentence"
                    style="margin-top: 0.5rem; height: 36px;">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span id="file-label">Seleccionar archivo</span>
            </button>
            
            {{-- Nombre del archivo seleccionado --}}
            <span id="file-count" style="color: var(--color-text-secondary); font-size: 0.875rem; margin-left: 0.75rem;"></span>
            
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
            const label = document.getElementById('file-label');
            const countSpan = document.getElementById('file-count');
            
            if (input.files && input.files[0]) {
                // Actualizar label del botón
                label.textContent = input.files[0].name;
                countSpan.textContent = '';
                
                // Preview de la imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                label.textContent = 'Seleccionar archivo';
                countSpan.textContent = '';
            }
        }
        </script>

        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
            <x-primary-button class="sn-sentence py-2 px-5">Guardar cambios</x-primary-button>
        </div>
    </form>