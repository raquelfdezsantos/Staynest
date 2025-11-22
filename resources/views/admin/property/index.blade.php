<x-app-layout>
    <div class="sn-reservar max-w-4xl mx-auto px-4 py-10 admin-slim-badges">
        <style>
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
                background-color: var(--color-accent);
                border-color: var(--color-accent);
            }
            .sn-checkbox:hover {
                border-color: var(--color-accent);
            }
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
        <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1.5rem;">Editar Propiedad</h3>

            <form method="POST" action="{{ route('admin.property.update', $property->id) }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
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
                        class="sn-input"
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
                            <input type="checkbox" name="services[]" value="wifi" {{ in_array('wifi', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">WiFi</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="parking" {{ in_array('parking', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Parking</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="pool" {{ in_array('pool', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Piscina</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="washer" {{ in_array('washer', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Lavadora</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="dishwasher" {{ in_array('dishwasher', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Lavavajillas</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="heating" {{ in_array('heating', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Calefacción</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="air_conditioning" {{ in_array('air_conditioning', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Aire acondicionado</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="hairdryer" {{ in_array('hairdryer', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Secador</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="first_aid_kit" {{ in_array('first_aid_kit', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Botiquín</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="pets_allowed" {{ in_array('pets_allowed', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Mascotas permitidas</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="smoking_allowed" {{ in_array('smoking_allowed', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Permitido fumar</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="tv" {{ in_array('tv', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">TV / Smart TV</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="kitchen" {{ in_array('kitchen', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Cocina equipada</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="towels" {{ in_array('towels', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Toallas incluidas</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="bed_linen" {{ in_array('bed_linen', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Ropa de cama incluida</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="terrace" {{ in_array('terrace', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Terraza / Balcón</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="elevator" {{ in_array('elevator', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
                            <span style="font-size: var(--text-sm); color: var(--color-text-primary);">Ascensor</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="crib" {{ in_array('crib', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0 focus:ring-offset-0 focus:border-[color:var(--color-accent)]" style="accent-color: var(--color-accent);">
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
            <h3 class="danger-title">⚠️ Zona de peligro</h3>
            
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

            <form 
                method="POST" 
                action="{{ route('admin.property.destroy', $property->id) }}"
                onsubmit="return confirmDelete(event, {{ $futureReservationsCount }})"
            >
                @csrf
                @method('DELETE')

                <button type="submit" class="btn-danger">
                    Dar de baja propiedad
                </button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(event, reservationsCount) {
            event.preventDefault();
            
            let message = reservationsCount > 0 
                ? `⚠️ ATENCIÓN: Hay ${reservationsCount} reserva(s) activa(s).\n\nSe cancelarán TODAS las reservas futuras y se procesarán reembolsos automáticamente.\n\n¿Estás SEGURO de que quieres dar de baja esta propiedad?`
                : '¿Estás seguro de que quieres dar de baja esta propiedad?';
            
            const firstConfirm = confirm(message);
            if (!firstConfirm) return false;
            
            const secondConfirm = confirm('Esta acción es reversible desde la base de datos.\n\n¿Confirmas que deseas continuar?');
            if (secondConfirm) {
                event.target.submit();
            }
            
            return false;
        }
    </script>
</x-app-layout>
