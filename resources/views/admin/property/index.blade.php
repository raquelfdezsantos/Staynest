<x-app-layout>
    <div class="sn-reservar max-w-4xl mx-auto px-4 py-10">
        
        {{-- Breadcrumbs --}}
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="text-neutral-400 hover:text-neutral-200 transition-colors">Dashboard</a>
                </li>
                <li class="text-neutral-500">/</li>
                <li class="text-neutral-200">Propiedad</li>
            </ol>
        </nav>

        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Gestión de Propiedad</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Edita la información básica de la propiedad.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error mb-6">{{ session('error') }}</div>
        @endif

        {{-- Alerta si la propiedad está dada de baja --}}
        @if($property->trashed())
            <div class="alert alert-error mb-6">
                <p class="font-semibold mb-1">⚠️ Esta propiedad está dada de baja</p>
                <p class="text-sm">Fue eliminada el {{ $property->deleted_at->format('d/m/Y H:i') }}</p>
            </div>
        @endif

        {{-- Formulario de edición --}}
        <div style="background: rgba(var(--color-bg-secondary-rgb), 0.8); border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); backdrop-filter: blur(10px); padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1.5rem;">Editar Propiedad</h3>

            <form method="POST" action="{{ route('admin.property.update', $property->id) }}" class="space-y-6">
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
                        class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                        placeholder="Describe tu alojamiento de forma detallada..."
                    >{{ old('description', $property->description) }}</textarea>
                    <p class="text-xs text-neutral-400 mt-1">Esta descripción se mostrará en la página pública del alojamiento.</p>
                </div>

                {{-- Servicios --}}
                <div>
                    <x-input-label value="Servicios disponibles" />
                    <p class="text-xs text-neutral-400 mb-3">Marca los servicios que ofrece tu alojamiento. Se mostrarán en la página pública.</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $services = old('services', $property->services ?? []);
                        @endphp
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="wifi" {{ in_array('wifi', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">WiFi</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="parking" {{ in_array('parking', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Parking</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="pool" {{ in_array('pool', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Piscina</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="washer" {{ in_array('washer', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Lavadora</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="dishwasher" {{ in_array('dishwasher', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Lavavajillas</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="heating" {{ in_array('heating', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Calefacción</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="air_conditioning" {{ in_array('air_conditioning', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Aire acondicionado</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="hairdryer" {{ in_array('hairdryer', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Secador</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="first_aid_kit" {{ in_array('first_aid_kit', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Botiquín</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="pets_allowed" {{ in_array('pets_allowed', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Mascotas permitidas</span>
                        </label>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="smoking_allowed" {{ in_array('smoking_allowed', $services) ? 'checked' : '' }} class="rounded border-neutral-700 bg-neutral-800 text-[color:var(--color-accent)] focus:outline-none focus:ring-0">
                            <span class="text-sm text-neutral-200">Permitido fumar</span>
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
                <div style="align-self:flex-start; margin-top:.5rem;">
                    <x-primary-button>Guardar cambios</x-primary-button>
                </div>
            </form>
        </div>

        {{-- Zona de peligro: Dar de baja propiedad --}}
        <div class="danger-zone">
            <div class="p-6">
                <h3 class="danger-title">⚠️ Zona de peligro</h3>
                
                <p class="danger-text">
                    Una vez que des de baja la propiedad, se cancelarán todas las reservas futuras activas 
                    y se procesarán los reembolsos automáticamente.
                </p>

                @if($futureReservationsCount > 0)
                    <div class="alert alert-warning mb-4">
                        <p class="font-semibold mb-1">{{ $futureReservationsCount }} reserva(s) futura(s) será(n) cancelada(s)</p>
                        <p class="text-sm">Los clientes recibirán un reembolso completo y un email de notificación.</p>
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
    </div>

    <style>
        /* === Admin Property Page Styling === */
        .admin-property-page {
            font-family: var(--font-sans);
        }

        .property-title {
            font-family: var(--font-serif);
            font-size: var(--text-3xl);
            font-weight: 400;
            color: var(--color-text-primary);
            margin-bottom: 0.5rem;
        }

        .property-subtitle {
            color: var(--color-text-secondary);
            font-size: var(--text-base);
        }

        .property-card {
            background: var(--color-bg-card);
            border: 1px solid var(--color-border-light);
            border-radius: 2px;
            overflow: hidden;
        }

        .card-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .form-hint {
            margin-top: 0.25rem;
            font-size: var(--text-xs);
            color: var(--color-text-muted);
        }

        .danger-zone {
            background: var(--color-bg-card);
            border: 2px solid var(--color-error);
            border-radius: 2px;
            overflow: hidden;
        }

        .danger-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--color-error);
            margin-bottom: 1rem;
        }

        .danger-text {
            font-size: var(--text-sm);
            color: var(--color-text-secondary);
            margin-bottom: 1rem;
        }

        .btn-danger {
            padding: 0.5rem 1.25rem;
            font-size: var(--text-sm);
            font-weight: 600;
            color: white;
            background-color: var(--color-error);
            border: none;
            border-radius: 2px;
            cursor: pointer;
            transition: background var(--transition-fast);
        }

        .btn-danger:hover {
            background-color: #d87876;
        }

        /* Light mode overrides */
        html[data-theme="light"] .admin-property-page .property-card,
        html[data-theme="light"] .admin-property-page .danger-zone {
            background-color: #fff;
        }
    </style>

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
