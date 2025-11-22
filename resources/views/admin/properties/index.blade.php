<x-app-layout>
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10 admin-slim-badges">
        
        {{-- Header centrado como en Dashboard --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Mis propiedades</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Gestiona tus alojamientos, fotos, disponibilidad y reservas.</p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        {{-- Botón crear nueva propiedad --}}
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.properties.create') }}" 
               class="btn-action btn-action-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="sn-sentence">Crear nueva propiedad</span>
            </a>
        </div>

        {{-- Grid de propiedades --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @forelse($properties as $property)
                <div style="border: 1px solid var(--color-border-light); border-radius: 2px; overflow: hidden; transition: border-color var(--transition-fast); background-color: var(--color-bg-primary);" class="property-card-hover">
                    {{-- Foto de portada --}}
                    <div style="position: relative; width: 100%; height: 200px; {{ $property->trashed() ? 'opacity: 0.6;' : '' }}">
                            @php
                                $coverPhoto = $property->photos->where('is_cover', true)->first() ?? $property->photos->first();
                            @endphp
                            
                            @if($coverPhoto)
                                <img 
                                    src="{{ str_starts_with($coverPhoto->url, 'http') ? $coverPhoto->url : asset('storage/' . $coverPhoto->url) }}" 
                                    alt="{{ $property->name }}"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                >
                            @else
                                <div style="width: 100%; height: 100%; background-color: var(--color-bg-secondary); display: flex; align-items: center; justify-content: center; color: var(--color-text-muted);">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                        {{-- Badge estado --}}
                        @if($property->trashed())
                            <div class="badge badge-error" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                <span class="sn-sentence">Dada de baja</span>
                            </div>
                        @else
                            <div class="badge badge-success" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                <span class="sn-sentence">Activa</span>
                            </div>
                        @endif
                    </div>

                    {{-- Información --}}
                    <div style="padding: 1.25rem 1.25rem 0; {{ $property->trashed() ? 'opacity: 0.6;' : '' }}">
                        <h3 style="font-family: var(--font-serif); font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin: 0 0 1rem 0;">
                            {{ $property->name }}
                        </h3>
                        <div style="font-size: var(--text-sm); color: var(--color-text-secondary); display: flex; flex-wrap: wrap; gap: 0.375rem; align-items: center;">
                            <span>{{ $property->capacity }} personas</span>
                            <span>•</span>
                            <span>{{ $property->photos->count() }} fotos</span>
                            @if($property->city)
                                <br>
                                <span>{{ $property->city }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    @if(!$property->trashed())
                        <div style="padding: 1rem 1.25rem 1.25rem;">
                            <a href="{{ route('admin.property.dashboard', $property->slug) }}" 
                               class="btn-action btn-action-primary" style="width: 100%; text-align: center;">
                                <span class="sn-sentence">Gestionar</span>
                            </a>
                        </div>
                    @else
                        <div style="padding: 1rem 1.25rem 1.25rem;">
                            <button type="button"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-restore-{{ $property->id }}')"
                                    class="btn-action btn-action-success" 
                                    style="width: 100%;">
                                <span class="sn-sentence">Restaurar propiedad</span>
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; color: var(--color-text-muted);">
                    <svg style="margin: 0 auto 1.5rem;" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 style="margin-bottom: 0.5rem; font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">No hay propiedades</h3>
                    <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin: 0;">Comienza creando una nueva propiedad.</p>
                </div>
            @endforelse
        </div>

        {{-- Modales de confirmación para restaurar --}}
        @foreach($properties as $property)
            @if($property->trashed())
                <x-modal name="confirm-restore-{{ $property->id }}" focusable>
                    <div style="padding: 2rem; border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.9); backdrop-filter: blur(10px);">
                        <form method="POST" action="{{ route('admin.properties.restore', $property->id) }}">
                            @csrf
                            @method('PATCH')

                            <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                                ¿Restaurar esta propiedad?
                            </h2>

                            <p style="margin-bottom: 1.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                                La propiedad "{{ $property->name }}" volverá a estar activa y visible en tu listado.
                            </p>

                            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                                <button type="button"
                                        @click="$dispatch('close-modal', 'confirm-restore-{{ $property->id }}')"
                                        class="btn-action btn-action-secondary"
                                        style="background-color: var(--color-bg-secondary); color: var(--color-text-primary); border: 1px solid var(--color-accent); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                                        onmouseover="this.style.color = 'var(--color-accent)'; this.style.backgroundColor = 'rgba(77, 141, 148, 0.10)'; this.style.border = 'none';"
                                        onmouseout="this.style.backgroundColor = 'var(--color-bg-secondary)'; this.style.color = 'var(--color-text-primary)'; this.style.border = '1px solid var(--color-accent)';">
                                    <span class="sn-sentence">Cancelar</span>
                                </button>

                                <button type="submit" 
                                        class="btn-action btn-action-success"
                                        style="background-color: var(--color-success); color: #fff; border: 1px solid var(--color-success); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                                        onmouseover="this.style.opacity = '0.9';"
                                        onmouseout="this.style.opacity = '1';">
                                    <span class="sn-sentence">Restaurar propiedad</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </x-modal>
            @endif
        @endforeach
    </div>

    <style>
        /* Badges compactos para admin */
        .admin-slim-badges .badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            letter-spacing: 0.05em;
        }
        .admin-slim-badges .badge-success,
        .admin-slim-badges .badge-warning,
        .admin-slim-badges .badge-error,
        .admin-slim-badges .badge-info {
            background: transparent !important;
            border: 1px solid var(--color-accent);
        }
        
        /* Quitar movimiento del botón success al hover */
        .btn-action-success:hover {
            transform: none !important;
        }
        
        /* Hover simple para property cards */
        .property-card-hover:hover {
            border-color: var(--color-accent) !important;
        }
    </style>
</x-app-layout>
