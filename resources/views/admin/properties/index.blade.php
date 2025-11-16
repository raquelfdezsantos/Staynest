<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header simple --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Gestión de Propiedades
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-sm);">
                Administra todas las propiedades, fotos, calendario y reservas.
            </p>
        </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Botón crear nueva propiedad --}}
        <div style="margin-bottom: 3rem;">
            <a href="{{ route('admin.properties.create') }}" 
               class="btn-action btn-action-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Nueva Propiedad
            </a>
        </div>

            {{-- Grid de propiedades - estilo minimalista --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                @forelse($properties as $property)
                    <div style="border: 1px solid var(--color-border-light); border-radius: var(--radius-base); overflow: hidden; transition: all var(--transition-fast); {{ $property->trashed() ? 'opacity: 0.6;' : '' }}">
                        {{-- Foto de portada --}}
                        <div style="position: relative;">
                            @php
                                $coverPhoto = $property->photos->where('is_cover', true)->first() ?? $property->photos->first();
                            @endphp
                            
                            @if($coverPhoto)
                                <img 
                                    src="{{ str_starts_with($coverPhoto->url, 'http') ? $coverPhoto->url : asset('storage/' . $coverPhoto->url) }}" 
                                    alt="{{ $property->name }}"
                                    style="width: 100%; height: 200px; object-fit: cover;"
                                >
                            @else
                                <div style="width: 100%; height: 200px; background-color: var(--color-bg-secondary); display: flex; align-items: center; justify-content: center;">
                                    <svg style="width: 3rem; height: 3rem; color: var(--color-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badge estado --}}
                            @if($property->trashed())
                                <div class="badge badge-error" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    DADA DE BAJA
                                </div>
                            @else
                                <div class="badge badge-success" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    ACTIVA
                                </div>
                            @endif
                        </div>

                        {{-- Información --}}
                        <div style="padding: 1.25rem;">
                            <h3 style="font-family: var(--font-serif); font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                                {{ $property->name }}
                            </h3>
                            <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 1rem;">
                                {{ $property->capacity }} personas • {{ $property->photos->count() }} fotos
                                @if($property->city)
                                    <br>{{ $property->city }}
                                @endif
                            </p>

                            {{-- Botones de acción --}}
                            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                                @if(!$property->trashed())
                                    <a href="{{ route('admin.properties.dashboard', $property->id) }}" 
                                       class="btn-action btn-action-primary" style="text-align: center; width: 100%;">
                                        Gestionar
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.properties.restore', $property->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn-action btn-action-success" 
                                                style="width: 100%;"
                                                onclick="return confirm('¿Restaurar esta propiedad?')">
                                            RESTAURAR PROPIEDAD
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <svg style="margin: 0 auto; width: 4rem; height: 4rem; color: var(--color-text-muted); margin-bottom: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 style="margin-bottom: 0.5rem; font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">No hay propiedades</h3>
                        <p style="color: var(--color-text-secondary); font-size: var(--text-sm);">Comienza creando una nueva propiedad.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
