<x-app-layout>
    <div class="sn-reservar max-w-4xl mx-auto px-4 py-10">
        {{-- Header centrado --}}
        <header style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Crear Nueva Propiedad</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Completa los datos del alojamiento. Podrás añadir fotos después desde la sección "Fotos".</p>
        </header>

        <form method="POST" action="{{ route('admin.properties.store') }}" novalidate>
            @csrf

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

            {{-- Formulario de creación --}}
            <div class="admin-form-card" style="border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); padding: 1.5rem; margin-bottom: 2rem;">
                <style>
                    .admin-form-card {
                        background: rgba(51, 51, 51, 0.2) !important;
                    }
                    html[data-theme="light"] .admin-form-card {
                        background: #E3E3E3 !important;
                    }
                    /* Textarea en modo oscuro */
                    html[data-theme="dark"] .admin-textarea {
                        background: #222222 !important;
                    }
                    .form-hint {
                        margin-top: 0.25rem;
                        font-size: var(--text-xs);
                        color: var(--color-text-muted);
                    }
                </style>
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1.5rem;">Datos del Alojamiento</h3>

                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    {{-- Nombre del alojamiento --}}
                    <div>
                        <x-input-label for="name" value="Nombre del alojamiento *" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus maxlength="150" />
                    </div>

                    {{-- Slug (auto-generado, pero editable) --}}
                    <div>
                        <x-input-label for="slug" value="URL personalizada *" />
                        <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" required maxlength="150" placeholder="apartamento-centro-madrid" />
                        <p class="form-hint">Se usará en la URL: /propiedad/tu-slug</p>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <x-input-label for="description" value="Descripción completa" />
                        <textarea 
                            id="description"
                            name="description"
                            rows="6"
                            class="sn-input admin-textarea"
                            style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                            placeholder="Describe tu alojamiento de forma detallada..."
                        >{{ old('description') }}</textarea>
                        <p class="form-hint">Esta descripción se mostrará en la página pública del alojamiento.</p>
                    </div>

                    {{-- Dirección del alojamiento --}}
                    <div>
                        <x-input-label for="address" value="Dirección" />
                        <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" maxlength="200" />
                    </div>

                    {{-- Ciudad --}}
                    <div>
                        <x-input-label for="city" value="Ciudad" />
                        <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" maxlength="100" />
                    </div>

                    {{-- Código Postal --}}
                    <div>
                        <x-input-label for="postal_code" value="Código Postal" />
                        <x-text-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" maxlength="10" placeholder="28013" />
                    </div>

                    {{-- Provincia --}}
                    <div>
                        <x-input-label for="province" value="Provincia" />
                        <x-text-input id="province" class="block mt-1 w-full" type="text" name="province" :value="old('province')" maxlength="100" placeholder="Madrid" />
                    </div>

                    {{-- Capacidad --}}
                    <div>
                        <x-input-label for="capacity" value="Capacidad (huéspedes) *" />
                        <x-text-input id="capacity" type="number" class="block mt-1 w-full" name="capacity" :value="old('capacity', 4)" required min="1" max="50" />
                    </div>

                    {{-- Licencia turística --}}
                    <div>
                        <x-input-label for="tourism_license" value="Nº Licencia Turística" />
                        <x-text-input id="tourism_license" class="block mt-1 w-full" type="text" name="tourism_license" :value="old('tourism_license')" placeholder="VT-28-0001234" maxlength="100" />
                        <p class="form-hint">Número de licencia turística oficial</p>
                    </div>

                    {{-- Registro de alquiler --}}
                    <div>
                        <x-input-label for="rental_registration" value="Nº Registro de Alquiler" />
                        <x-text-input id="rental_registration" class="block mt-1 w-full" type="text" name="rental_registration" :value="old('rental_registration')" placeholder="ATR-28-001234-2024" maxlength="100" />
                        <p class="form-hint">Número de registro único de alquiler</p>
                    </div>

                    {{-- Botón de crear --}}
                    <div style="align-self: flex-start; padding-top: 0.5rem;">
                        <button type="submit" class="btn-action btn-action-primary sn-sentence" style="height: 36px; min-height: 36px; padding: 0 1.25rem; display: inline-flex; align-items: center; justify-content: center;">Crear Propiedad</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Auto-generar slug desde el nombre
        document.getElementById('name').addEventListener('input', function(e) {
            const name = e.target.value;
            const slug = name
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Quitar acentos
                .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
                .trim()
                .replace(/\s+/g, '-'); // Espacios a guiones
            
            document.getElementById('slug').value = slug;
        });
    </script>
</x-app-layout>
