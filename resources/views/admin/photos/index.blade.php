<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="font-family: var(--font-sans); color: var(--color-text-primary);">
            {{ __('Gestión de Fotos') }}
        </h2>
    </x-slot>

    <div class="admin-photos-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-900 text-green-100 border border-green-700" style="border-radius: 2px;">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-900 text-red-100 border border-red-700" style="border-radius: 2px;">{{ session('error') }}</div>
            @endif

            {{-- Formulario de subida --}}
            <div class="bg-neutral-800 border border-neutral-700 shadow-sm mb-6" style="border-radius: 2px; overflow: hidden;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="font-family: var(--font-sans); color: var(--color-text-primary);">Subir nuevas fotos</h3>
                    
                    <form method="POST" action="{{ route('admin.photos.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        
                        <div class="mb-4">
                            <label for="photos" class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">
                                Selecciona hasta 10 fotos (JPG, PNG, WEBP - máx. 5MB cada una)
                            </label>
                            <input 
                                type="file" 
                                name="photos[]" 
                                id="photos"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                required
                                class="admin-file-input block w-full text-sm"
                                style="color: var(--color-text-primary);"
                            >
                            @error('photos')
                                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-action btn-action-primary">
                            Subir fotos
                        </button>
                    </form>
                </div>
            </div>

            {{-- Galería de fotos --}}
            <div class="bg-neutral-800 border border-neutral-700 shadow-sm" style="border-radius: 2px; overflow: hidden;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="font-family: var(--font-sans); color: var(--color-text-primary);">Galería ({{ $photos->count() }} fotos)</h3>

                    @if($photos->isEmpty())
                        <p class="text-center py-8" style="color: var(--color-text-secondary);">No hay fotos subidas. Sube la primera foto usando el formulario de arriba.</p>
                    @else
                        <div id="photos-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($photos as $photo)
                                <div class="photo-item photo-card" data-photo-id="{{ $photo->id }}">
                                    {{-- Imagen --}}
                                    <img 
                                        src="{{ str_starts_with($photo->url, 'http') ? $photo->url : asset('storage/' . $photo->url) }}" 
                                        alt="Foto {{ $photo->id }}"
                                        class="photo-img"
                                    >

                                    {{-- Badge de portada --}}
                                    @if($photo->is_cover)
                                        <div class="photo-badge photo-badge-cover">
                                            ★ Portada
                                        </div>
                                    @endif

                                    {{-- Número de orden --}}
                                    <div class="photo-badge photo-badge-order">
                                        #{{ $photo->sort_order }}
                                    </div>

                                    {{-- Botones de acción --}}
                                    <div class="photo-actions">
                                        {{-- Botón marcar como portada --}}
                                        @if(!$photo->is_cover)
                                            <form method="POST" action="{{ route('admin.photos.set-cover', $photo->id) }}" class="inline-block">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    title="Marcar como portada"
                                                    class="btn-photo btn-photo-star"
                                                >
                                                    ★
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Botón eliminar --}}
                                        <form method="POST" action="{{ route('admin.photos.destroy', $photo->id) }}" class="inline-block" onsubmit="return confirm('¿Eliminar esta foto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                title="Eliminar foto"
                                                class="btn-photo btn-photo-delete"
                                            >
                                                🗑️
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Handle para drag --}}
                                    <div class="drag-handle" title="Arrastra para reordenar">
                                        ⠿ ARRASTRAR ⠿
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="text-sm mt-4" style="color: var(--color-text-secondary);">
                            💡 <strong>Tip:</strong> Arrastra las fotos desde la barra inferior "ARRASTRAR" para reordenarlas. Haz clic en ★ para marcar como portada o 🗑️ para eliminar.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($photos->isNotEmpty())
    <style>
        /* === Admin Photos Page Styling === */
        .admin-photos-page .btn-action {
            font-size: var(--text-sm);
            padding: 0.5rem 1.25rem;
            border-radius: 2px;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 600;
            line-height: 1.25rem;
        }

        .admin-photos-page .btn-action-primary {
            background-color: var(--color-accent);
            color: #fff;
            border: 1px solid var(--color-accent);
        }

        .admin-photos-page .btn-action-primary:hover {
            background-color: #3d7178;
            border-color: #3d7178;
        }

        .admin-photos-page .btn-action-secondary {
            background-color: transparent;
            color: var(--color-text-primary);
            border: 1px solid var(--color-border-light);
        }

        .admin-photos-page .btn-action-secondary:hover {
            background-color: var(--color-bg-tertiary);
            border-color: var(--color-text-secondary);
        }

        /* File input styling */
        .admin-file-input::file-selector-button {
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 2px;
            border: none;
            font-size: 0.875rem;
            font-weight: 600;
            background-color: var(--color-accent);
            color: #fff;
            cursor: pointer;
        }

        .admin-file-input::file-selector-button:hover {
            background-color: #3d7178;
        }

        /* Photo cards */
        .photo-card {
            position: relative;
            min-height: 280px;
            border: 1px solid var(--color-border-light);
            border-radius: 2px;
            overflow: hidden;
            background-color: var(--color-bg-secondary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .photo-img {
            width: 100%;
            height: 12rem;
            object-fit: cover;
            display: block;
        }

        .photo-badge {
            position: absolute;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 2px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            z-index: 20;
        }

        .photo-badge-cover {
            top: 8px;
            left: 8px;
            background-color: #eab308;
            color: white;
        }

        .photo-badge-order {
            bottom: 48px;
            right: 8px;
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
        }

        .photo-actions {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            gap: 4px;
            z-index: 30;
        }

        .btn-photo {
            padding: 6px 10px;
            color: white;
            border: none;
            border-radius: 2px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-photo-star {
            background-color: #eab308;
        }

        .btn-photo-star:hover {
            background-color: #ca8a04;
        }

        .btn-photo-delete {
            background-color: #ef4444;
        }

        .btn-photo-delete:hover {
            background-color: #dc2626;
        }

        .drag-handle {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background-color: var(--color-accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.2);
            z-index: 25;
        }

        .drag-handle:hover {
            background-color: #3d7178;
        }

        /* SortableJS states */
        .sortable-ghost {
            opacity: 0.4;
            background-color: rgba(77, 141, 148, 0.2);
        }

        .sortable-drag {
            opacity: 1;
            background-color: var(--color-bg-secondary);
            transform: scale(1.05);
        }

        .sortable-chosen {
            border: 2px solid var(--color-accent) !important;
            box-shadow: 0 0 20px rgba(77, 141, 148, 0.5);
        }

        /* Light mode overrides */
        html[data-theme="light"] .admin-photos-page .bg-neutral-800 {
            background-color: var(--color-bg-secondary) !important;
        }

        html[data-theme="light"] .admin-photos-page .border-neutral-700 {
            border-color: var(--color-border-light) !important;
        }

        html[data-theme="light"] .admin-photos-page .photo-card {
            background-color: #fff;
            border-color: var(--color-border-light);
        }

        html[data-theme="light"] .admin-photos-page .photo-badge-order {
            background-color: #e5e7eb;
            color: #000;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const grid = document.getElementById('photos-grid');
            
            if (!grid) {
                console.error('Grid not found');
                return;
            }

            // Verificar que SortableJS está cargado
            if (typeof Sortable === 'undefined') {
                console.error('SortableJS not loaded');
                alert('Error: La librería de arrastre no se cargó correctamente. Recarga la página.');
                return;
            }
            
            // Inicializar SortableJS para drag & drop
            const sortable = new Sortable(grid, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                forceFallback: true,
                
                onStart: function(evt) {
                    console.log('Drag started');
                },
                
                onEnd: function(evt) {
                    console.log('Drag ended');
                    
                    // Obtener nuevo orden
                    const photoItems = grid.querySelectorAll('.photo-item');
                    const newOrder = Array.from(photoItems).map(item => {
                        const id = item.dataset.photoId;
                        console.log('Photo ID:', id);
                        return id;
                    });
                    
                    console.log('New order:', newOrder);
                    
                    // Enviar al servidor
                    fetch('{{ route("admin.photos.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: newOrder })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            // Recargar para actualizar números de orden
                            location.reload();
                        } else {
                            alert('Error al reordenar las fotos');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar el nuevo orden. Recarga la página.');
                    });
                }
            });
            
            console.log('Sortable initialized successfully');
        });
    </script>
    @endif
</x-app-layout>
