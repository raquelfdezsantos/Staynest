<x-app-layout>
    <div class="sn-reservar max-w-4xl mx-auto px-4 py-10 admin-slim-badges">
        <style>
            /* Alpine.js x-cloak */
            [x-cloak] { display: none !important; }
            
            /* Admin: badges discretos, monocromos y accesibles */
            .admin-slim-badges .badge {
                text-transform: none;
                letter-spacing: 0;
                font-size: 0.6875rem;
                padding: 0.25rem 0.5rem;
                border-radius: 2px;
                font-weight: 600;
                color: var(--color-text-primary);
                background: transparent !important;
                border: 1px solid var(--color-accent);
            }
            
            /* Form hints */
            .form-hint {
                margin-top: 0.25rem;
                font-size: var(--text-xs);
                color: var(--color-text-muted);
            }
            
            /* Checkboxes personalizados */
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
            .sn-checkbox:focus {
                outline: none;
                ring: 0;
                ring-offset: 0;
                box-shadow: none;
                border-color: var(--color-accent);
            }
            
            /* Botones consistentes */
            .btn-action {
                padding: 0.5rem 1rem;
                min-height: 36px;
                line-height: 1;
                font-size: var(--text-sm);
                font-weight: 600;
                border-radius: 2px;
                transition: all var(--transition-fast);
            }
        </style>

        {{-- Header centrado --}}
        <header style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Crear Nueva Propiedad</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Completa los datos del alojamiento. Podrás añadir fotos y gestionar el calendario después.</p>
        </header>

        <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data" novalidate>
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
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus maxlength="150" />
                    </div>

                    {{-- Slug (auto-generado, pero editable) --}}
                    <div>
                        <x-input-label for="slug" value="URL personalizada *" />
                        <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" maxlength="150" placeholder="apartamento-centro-madrid" />
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

                    {{-- Servicios --}}
                    <div>
                        <x-input-label value="Servicios disponibles" />
                        <p class="form-hint" style="margin-bottom: 0.75rem;">Marca los servicios que ofrece tu alojamiento. Se mostrarán en la página pública.</p>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem;">
                            @php
                                $services = old('services', []);
                            @endphp
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="wifi" {{ in_array('wifi', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">WiFi</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="parking" {{ in_array('parking', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Parking</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="pool" {{ in_array('pool', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Piscina</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="washer" {{ in_array('washer', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Lavadora</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="dishwasher" {{ in_array('dishwasher', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Lavavajillas</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="heating" {{ in_array('heating', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Calefacción</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="air_conditioning" {{ in_array('air_conditioning', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Aire acondicionado</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="hairdryer" {{ in_array('hairdryer', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Secador</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="first_aid_kit" {{ in_array('first_aid_kit', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Botiquín</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="pets_allowed" {{ in_array('pets_allowed', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Mascotas permitidas</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="smoking_allowed" {{ in_array('smoking_allowed', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Permitido fumar</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="tv" {{ in_array('tv', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">TV / Smart TV</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="kitchen" {{ in_array('kitchen', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Cocina equipada</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="towels" {{ in_array('towels', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Toallas incluidas</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="bed_linen" {{ in_array('bed_linen', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Ropa de cama incluida</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="terrace" {{ in_array('terrace', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Terraza / Balcón</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="elevator" {{ in_array('elevator', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Ascensor</span>
                            </label>
                            
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="services[]" value="crib" {{ in_array('crib', $services) ? 'checked' : '' }} class="sn-checkbox">
                                <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Cuna disponible</span>
                            </label>
                        </div>
                    </div>

                    {{-- Dirección del alojamiento --}}
                    <div>
                        <x-input-label for="address" value="Dirección" />
                        <textarea 
                            id="address"
                            name="address" 
                            rows="2"
                            maxlength="200"
                            class="sn-input block mt-1 w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            style="resize: vertical; min-height: 60px;">{{ old('address') }}</textarea>
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
                        <x-text-input id="capacity" type="number" class="block mt-1 w-full" name="capacity" :value="old('capacity', 4)" min="1" max="50" />
                    </div>

                    {{-- Licencia turística --}}
                    <div>
                        <x-input-label for="tourism_license" value="Nº Licencia Turística *" />
                        <textarea 
                            id="tourism_license"
                            name="tourism_license" 
                            rows="2"
                            maxlength="100"
                            placeholder="VT-28-0001234"
                            class="sn-input block mt-1 w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            style="resize: vertical; min-height: 60px;">{{ old('tourism_license') }}</textarea>
                        <p class="form-hint">Número de licencia turística oficial</p>
                    </div>

                    {{-- Registro de alquiler --}}
                    <div>
                        <x-input-label for="rental_registration" value="Nº Registro de Alquiler *" />
                        <textarea 
                            id="rental_registration"
                            name="rental_registration" 
                            rows="2"
                            maxlength="100"
                            placeholder="ATR-28-001234-2024"
                            class="sn-input block mt-1 w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                            style="resize: vertical; min-height: 60px;">{{ old('rental_registration') }}</textarea>
                        <p class="form-hint">Número de registro único de alquiler</p>
                    </div>
                </div>
            </div>

            {{-- Sección: Entorno --}}
            <div class="admin-form-card" style="border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Información del Entorno (Opcional)</h3>
                <p class="form-hint" style="margin-bottom: 1.5rem;">Describe el entorno que rodea tu propiedad para que los huéspedes conozcan mejor la zona.</p>

                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    {{-- Header / Hero --}}
                    <div style="padding-bottom: 1.5rem; border-bottom: 1px solid rgba(var(--color-border-rgb), 0.2);">
                        <h4 style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Encabezado</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <x-input-label for="env_title" value="Título principal" />
                                <x-text-input 
                                    id="env_title"
                                    name="env_title" 
                                    class="block mt-1 w-full"
                                    :value="old('env_title')"
                                    placeholder="Ej: Descubre Gijón"
                                    maxlength="100"
                                />
                            </div>

                            <div>
                                <x-input-label for="env_subtitle" value="Subtítulo" />
                                <textarea 
                                    name="env_subtitle" 
                                    id="env_subtitle"
                                    rows="2"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Breve descripción del entorno..."
                                >{{ old('env_subtitle') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="env_summary" value="Resumen (viñetas)" />
                                <textarea 
                                    name="env_summary" 
                                    id="env_summary"
                                    rows="3"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Puntos clave separados por salto de línea&#10;Playa de Poniente a 9 min&#10;Centro histórico cercano&#10;Gastronomía local"
                                >{{ old('env_summary') }}</textarea>
                                <p class="form-hint">Cada línea será una viñeta</p>
                            </div>

                            <div>
                                <x-input-label for="env_hero_photo" value="Foto destacada" />
                                <input 
                                    type="file" 
                                    name="env_hero_photo" 
                                    id="env_hero_photo"
                                    accept="image/*"
                                    style="display: none;"
                                />
                                <button type="button" onclick="document.getElementById('env_hero_photo').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px; margin-top: 0.25rem;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="env_hero_photo_label">Elegir archivo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque: Naturaleza --}}
                    <div style="padding-bottom: 1.5rem; border-bottom: 1px solid rgba(var(--color-border-rgb), 0.2);">
                        <h4 style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Naturaleza</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <x-input-label for="env_nature_description" value="Descripción" />
                                <textarea 
                                    name="env_nature_description" 
                                    id="env_nature_description"
                                    rows="3"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Describe la naturaleza y paisajes cercanos..."
                                >{{ old('env_nature_description') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="env_nature_photo" value="Foto" />
                                <input 
                                    type="file" 
                                    name="env_nature_photo" 
                                    id="env_nature_photo"
                                    accept="image/*"
                                    style="display: none;"
                                />
                                <button type="button" onclick="document.getElementById('env_nature_photo').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px; margin-top: 0.25rem;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="env_nature_photo_label">Elegir archivo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque: Cultura y Patrimonio --}}
                    <div style="padding-bottom: 1.5rem; border-bottom: 1px solid rgba(var(--color-border-rgb), 0.2);">
                        <h4 style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Cultura y Patrimonio</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <x-input-label for="env_culture_description" value="Descripción" />
                                <textarea 
                                    name="env_culture_description" 
                                    id="env_culture_description"
                                    rows="3"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Describe el patrimonio cultural y lugares históricos..."
                                >{{ old('env_culture_description') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="env_culture_photo" value="Foto" />
                                <input 
                                    type="file" 
                                    name="env_culture_photo" 
                                    id="env_culture_photo"
                                    accept="image/*"
                                    style="display: none;"
                                />
                                <button type="button" onclick="document.getElementById('env_culture_photo').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px; margin-top: 0.25rem;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="env_culture_photo_label">Elegir archivo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque: Actividades --}}
                    <div style="padding-bottom: 1.5rem; border-bottom: 1px solid rgba(var(--color-border-rgb), 0.2);">
                        <h4 style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Actividades</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <x-input-label for="env_activities_description" value="Descripción" />
                                <textarea 
                                    name="env_activities_description" 
                                    id="env_activities_description"
                                    rows="3"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Describe las actividades disponibles en la zona..."
                                >{{ old('env_activities_description') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="env_activities_photo" value="Foto" />
                                <input 
                                    type="file" 
                                    name="env_activities_photo" 
                                    id="env_activities_photo"
                                    accept="image/*"
                                    style="display: none;"
                                />
                                <button type="button" onclick="document.getElementById('env_activities_photo').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px; margin-top: 0.25rem;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="env_activities_photo_label">Elegir archivo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bloque: Servicios Cercanos --}}
                    <div>
                        <h4 style="font-size: var(--text-base); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Servicios Cercanos</h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <x-input-label for="env_services_description" value="Descripción" />
                                <textarea 
                                    name="env_services_description" 
                                    id="env_services_description"
                                    rows="3"
                                    class="sn-input admin-textarea"
                                    style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                                    placeholder="Describe los servicios esenciales cercanos..."
                                >{{ old('env_services_description') }}</textarea>
                            </div>

                            <div>
                                <x-input-label for="env_services_photo" value="Foto" />
                                <input 
                                    type="file" 
                                    name="env_services_photo" 
                                    id="env_services_photo"
                                    accept="image/*"
                                    style="display: none;"
                                />
                                <button type="button" onclick="document.getElementById('env_services_photo').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px; margin-top: 0.25rem;">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="env_services_photo_label">Elegir archivo</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Botón de crear al final --}}
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

        // JavaScript para actualizar el texto de los botones de archivo
        const fileInputs = [
            'env_hero_photo',
            'env_nature_photo',
            'env_culture_photo',
            'env_activities_photo',
            'env_services_photo'
        ];

        fileInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            const label = document.getElementById(inputId + '_label');
            
            if (input && label) {
                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        label.textContent = this.files[0].name;
                    } else {
                        label.textContent = 'Elegir archivo';
                    }
                });
            }
        });
    </script>
</x-app-layout>
