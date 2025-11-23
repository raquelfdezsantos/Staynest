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
            
            /* Checkboxes usan estilo global .sn-checkbox definido en staynest.css */
            /* Checkboxes personalizados (administración) */
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
            
            /* Danger zone estilo toast sutil */
            .danger-zone {
                background: rgba(204, 89, 86, 0.15);
                border: 1px solid rgba(204, 89, 86, 0.3);
                border-radius: var(--radius-base);
                padding: 1rem 1.25rem;
                margin-top: 4rem;
                font-size: var(--text-sm);
                line-height: 1.5;
            }
            .danger-title {
                font-size: var(--text-base);
                font-weight: 600;
                color: var(--color-text-primary);
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .danger-text {
                font-size: var(--text-sm);
                color: var(--color-text-secondary);
                margin-bottom: 1rem;
            }
            .btn-danger {
                padding: 0.5rem 1rem;
                height: 36px;
                min-height: 36px;
                font-size: var(--text-sm);
                font-weight: 600;
                color: #fff;
                background-color: var(--color-error);
                border: 1px solid var(--color-error);
                border-radius: 2px;
                cursor: pointer;
                transition: all var(--transition-fast);
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .btn-danger:hover {
                background-color: rgba(204, 89, 86, 0.85);
            }
        </style>

        {{-- Header centrado --}}
        <header style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Gestión de Propiedad</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Edita la información básica de la propiedad.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        {{-- Alerta si la propiedad está dada de baja --}}
        @if($property->trashed())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <p style="font-weight: 600; margin-bottom: 0.25rem;">⚠️ Esta propiedad está dada de baja</p>
                <p style="font-size: var(--text-sm);">Fue eliminada el {{ $property->deleted_at->format('d/m/Y H:i') }}</p>
            </div>
        @endif

        {{-- Formulario de edición --}}
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
            </style>
            <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1.5rem;">Editar Propiedad</h3>

            <form method="POST" action="{{ route('admin.property.update', $property->slug) }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf
                @method('PUT')

                {{-- Nombre --}}
                <div>
                    <x-input-label for="name" value="Nombre de la propiedad *" />
                    <x-text-input 
                        id="name"
                        name="name" 
                        class="block mt-1 w-full"
                        :value="old('name', $property->name)"
                        required
                        maxlength="150"
                    />
                </div>

                {{-- Descripción --}}
                <div>
                    <x-input-label for="description" value="Descripción completa" />
                    <textarea 
                        name="description" 
                        id="description"
                        rows="6"
                        class="sn-input admin-textarea"
                        style="width: 100%; background: var(--color-bg-elevated); border: 1px solid var(--color-border-light); border-radius: 2px; padding: 0.75rem; color: var(--color-text-primary); font-size: var(--text-base);"
                        placeholder="Describe tu alojamiento de forma detallada..."
                    >{{ old('description', $property->description) }}</textarea>
                    <p class="form-hint">Esta descripción se mostrará en la página pública del alojamiento.</p>
                </div>

                {{-- Servicios --}}
                <div>
                    <x-input-label value="Servicios disponibles" />
                    <p class="form-hint" style="margin-bottom: 0.75rem;">Marca los servicios que ofrece tu alojamiento. Se mostrarán en la página pública.</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem;">
                        @php
                            $services = old('services', $property->services ?? []);
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

                {{-- Dirección --}}
                <div>
                    <x-input-label for="address" value="Dirección" />
                    <x-text-input 
                        id="address"
                        name="address" 
                        class="block mt-1 w-full"
                        :value="old('address', $property->address)"
                        maxlength="200"
                    />
                </div>

                {{-- Ciudad --}}
                <div>
                    <x-input-label for="city" value="Ciudad" />
                    <x-text-input 
                        id="city"
                        name="city" 
                        class="block mt-1 w-full"
                        :value="old('city', $property->city)"
                        maxlength="100"
                    />
                </div>

                {{-- Código Postal --}}
                <div>
                    <x-input-label for="postal_code" value="Código Postal" />
                    <x-text-input 
                        id="postal_code"
                        name="postal_code" 
                        class="block mt-1 w-full"
                        :value="old('postal_code', $property->postal_code)"
                        maxlength="10"
                        placeholder="28013"
                    />
                </div>

                {{-- Provincia --}}
                <div>
                    <x-input-label for="province" value="Provincia" />
                    <x-text-input 
                        id="province"
                        name="province" 
                        class="block mt-1 w-full"
                        :value="old('province', $property->province)"
                        maxlength="100"
                        placeholder="Madrid"
                    />
                </div>

                {{-- Capacidad --}}
                <div>
                    <x-input-label for="capacity" value="Capacidad (huéspedes) *" />
                    <x-text-input 
                        id="capacity"
                        type="number"
                        name="capacity" 
                        class="block mt-1 w-full"
                        :value="old('capacity', $property->capacity)"
                        required
                        min="1"
                        max="20"
                    />
                </div>

                {{-- Licencia turística --}}
                <div>
                    <x-input-label for="tourism_license" value="Nº Licencia Turística" />
                    <x-text-input 
                        id="tourism_license"
                        name="tourism_license" 
                        class="block mt-1 w-full"
                        :value="old('tourism_license', $property->tourism_license)"
                        maxlength="100"
                        placeholder="VT-28-0001234"
                    />
                    <p class="form-hint">Número de licencia turística oficial</p>
                </div>

                {{-- Registro de alquiler --}}
                <div>
                    <x-input-label for="rental_registration" value="Nº Registro de Alquiler" />
                    <x-text-input 
                        id="rental_registration"
                        name="rental_registration" 
                        class="block mt-1 w-full"
                        :value="old('rental_registration', $property->rental_registration)"
                        maxlength="100"
                        placeholder="ATR-28-001234-2024"
                    />
                    <p class="form-hint">Número de registro único de alquiler</p>
                </div>

                {{-- Botón de guardar --}}
                <div style="align-self: flex-start;">
                    <button type="submit" class="btn-action btn-action-primary sn-sentence">Guardar cambios</button>
                </div>
            </form>
        </div>

        {{-- Zona de peligro: Dar de baja propiedad --}}
        <div class="danger-zone">
            <h3 class="danger-title">
                <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="var(--color-error)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Zona de peligro
            </h3>
            
            <p class="danger-text">
                Una vez que des de baja la propiedad, se cancelarán todas las reservas futuras activas 
                y se procesarán los reembolsos automáticamente.
            </p>

            @if($futureReservationsCount > 0)
                <div style="background: rgba(234, 179, 8, 0.15); border: 1px solid rgba(234, 179, 8, 0.3); border-radius: var(--radius-base); padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: var(--text-sm); line-height: 1.5;">
                    <p style="font-weight: 600; margin-bottom: 0.25rem; color: var(--color-text-primary);">{{ $futureReservationsCount }} reserva(s) futura(s) será(n) cancelada(s)</p>
                    <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">Los clientes recibirán un reembolso completo y un email de notificación.</p>
                </div>
            @endif

            <button 
                type="button"
                class="btn-danger"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-property')"
            >
                Dar de baja propiedad
            </button>
            <x-modal name="confirm-delete-property" focusable>
                <div style="padding: 2rem; border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.9); backdrop-filter: blur(10px);">
                    <form method="POST" action="{{ route('admin.property.destroy', $property->slug) }}">
                        @csrf
                        @method('DELETE')
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
                            <svg style="width: 24px; height: 24px; flex-shrink: 0; margin-top: 0.125rem;" fill="none" stroke="var(--color-error)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div style="flex: 1;">
                                <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                                    ¿Dar de baja esta propiedad?
                                </h2>
                            </div>
                        </div>
                        <p style="margin-bottom: 1rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                            @if($futureReservationsCount > 0)
                                <strong>ATENCIÓN:</strong> Hay {{ $futureReservationsCount }} reserva(s) activa(s).<br><br>
                                Se cancelarán TODAS las reservas futuras y se procesarán reembolsos automáticamente.
                            @else
                                Se dará de baja la propiedad. Esta acción es reversible desde la base de datos.
                            @endif
                        </p>
                        <p style="margin-bottom: 1.5rem; font-size: var(--text-sm); color: var(--color-text-secondary);">
                            ¿Estás SEGURO de que quieres continuar?
                        </p>
                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button"
                                    x-on:click="$dispatch('close-modal', 'confirm-delete-property')"
                                    class="btn-action btn-action-secondary sn-sentence"
                                    style="height: 36px; padding: 0 1.25rem;">
                                No, volver
                            </button>
                            <button type="submit" 
                                    style="height: 36px; padding: 0 1.25rem; font-size: var(--text-sm); font-weight: 600; color: white; background-color: var(--color-error); border: none; border-radius: 2px; cursor: pointer; transition: background-color var(--transition-fast);"
                                    onmouseover="this.style.backgroundColor='#d87876'"
                                    onmouseout="this.style.backgroundColor='var(--color-error)'">
                                Sí, dar de baja
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
