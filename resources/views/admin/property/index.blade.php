<x-app-layout>
    <div class="max-w-4xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Gestión de Propiedad
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">
                Edita la información básica de la propiedad.
            </p>
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
        <div style="background: var(--color-bg-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-base); overflow: hidden; margin-bottom: 2rem;">
            <div style="padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1.5rem;">Editar Propiedad</h3>

                <form method="POST" action="{{ route('admin.property.update', $property->id) }}">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        {{-- Nombre --}}
                        <div>
                            <label for="name" class="form-label">
                                Nombre de la propiedad *
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name"
                                value="{{ old('name', $property->name) }}"
                                required
                                maxlength="150"
                                class="form-input"
                            >
                            @error('name')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label for="description" class="form-label">
                                Descripción
                            </label>
                            <textarea 
                                name="description" 
                                id="description"
                                rows="5"
                                class="form-input"
                            >{{ old('description', $property->description) }}</textarea>
                            @error('description')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dirección --}}
                        <div>
                            <label for="address" class="form-label">
                                Dirección
                            </label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address"
                                value="{{ old('address', $property->address) }}"
                                maxlength="200"
                                class="form-input"
                            >
                            @error('address')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ciudad --}}
                        <div>
                            <label for="city" class="form-label">
                                Ciudad
                            </label>
                            <input 
                                type="text" 
                                name="city" 
                                id="city"
                                value="{{ old('city', $property->city) }}"
                                maxlength="100"
                                class="form-input"
                            >
                            @error('city')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Código Postal --}}
                        <div>
                            <label for="postal_code" class="form-label">
                                Código Postal
                            </label>
                            <input 
                                type="text" 
                                name="postal_code" 
                                id="postal_code"
                                value="{{ old('postal_code', $property->postal_code) }}"
                                maxlength="10"
                                placeholder="28013"
                                class="form-input"
                            >
                            @error('postal_code')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Provincia --}}
                        <div>
                            <label for="province" class="form-label">
                                Provincia
                            </label>
                            <input 
                                type="text" 
                                name="province" 
                                id="province"
                                value="{{ old('province', $property->province) }}"
                                maxlength="100"
                                placeholder="Madrid"
                                class="form-input"
                            >
                            @error('province')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Capacidad --}}
                        <div>
                            <label for="capacity" class="form-label">
                                Capacidad (huéspedes) *
                            </label>
                            <input 
                                type="number" 
                                name="capacity" 
                                id="capacity"
                                value="{{ old('capacity', $property->capacity) }}"
                                required
                                min="1"
                                max="20"
                                class="form-input"
                            >
                            @error('capacity')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Licencia turística --}}
                        <div>
                            <label for="tourism_license" class="form-label">
                                Nº Licencia Turística
                            </label>
                            <input 
                                type="text" 
                                name="tourism_license" 
                                id="tourism_license"
                                value="{{ old('tourism_license', $property->tourism_license) }}"
                                maxlength="100"
                                placeholder="VT-28-0001234"
                                class="form-input"
                            >
                            <p style="margin-top: 0.25rem; font-size: var(--text-xs); color: var(--color-text-muted);">Número de licencia turística oficial</p>
                            @error('tourism_license')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Registro de alquiler --}}
                        <div>
                            <label for="rental_registration" class="form-label">
                                Nº Registro de Alquiler
                            </label>
                            <input 
                                type="text" 
                                name="rental_registration" 
                                id="rental_registration"
                                value="{{ old('rental_registration', $property->rental_registration) }}"
                                maxlength="100"
                                placeholder="ATR-28-001234-2024"
                                class="form-input"
                            >
                            <p style="margin-top: 0.25rem; font-size: var(--text-xs); color: var(--color-text-muted);">Número de registro único de alquiler</p>
                            @error('rental_registration')
                                <p style="margin-top: 0.25rem; font-size: var(--text-sm); color: var(--color-error);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botón de guardar --}}
                        <div>
                            <button type="submit" class="btn btn-primary">
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Zona de peligro: Dar de baja propiedad --}}
        <div style="background: var(--color-bg-card); border: 2px solid var(--color-error); border-radius: var(--radius-base); overflow: hidden;">
            <div style="padding: 1.5rem;">
                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-error); margin-bottom: 1rem;">⚠️ Zona de peligro</h3>
                
                <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 1rem;">
                    Una vez que des de baja la propiedad, se cancelarán todas las reservas futuras activas 
                    y se procesarán los reembolsos automáticamente.
                </p>

                @if($futureReservationsCount > 0)
                    <div class="alert alert-warning" style="margin-bottom: 1rem;">
                        <p style="font-weight: 600; margin-bottom: 0.25rem;">{{ $futureReservationsCount }} reserva(s) futura(s) será(n) cancelada(s)</p>
                        <p style="font-size: var(--text-sm);">Los clientes recibirán un reembolso completo y un email de notificación.</p>
                    </div>
                @endif

                <form 
                    method="POST" 
                    action="{{ route('admin.property.destroy', $property->id) }}"
                    onsubmit="return confirmDelete(event, {{ $futureReservationsCount }})"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit" style="padding: 0.5rem 1.25rem; font-size: var(--text-sm); font-weight: 600; color: white; background-color: var(--color-error); border: none; border-radius: 2px; cursor: pointer; transition: background var(--transition-fast);" onmouseover="this.style.backgroundColor='#d87876'" onmouseout="this.style.backgroundColor='var(--color-error)'">
                        Dar de baja propiedad
                    </button>
                </form>
            </div>
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
