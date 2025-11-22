<x-app-layout>
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10 admin-slim-badges">
        
        {{-- Header centrado como en Dashboard --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Galería de fotos</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Gestiona las imágenes de tu propiedad, marca la portada y organízalas.</p>
        </header>

        {{-- Mensajes --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            {{-- Formulario de subida --}}
            <div class="bg-neutral-800 border border-neutral-700 shadow-sm mb-6" style="border-radius: 2px; overflow: hidden;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="font-family: var(--font-sans); color: var(--color-text-primary);">Subir nuevas fotos</h3>
                    
                    <form method="POST" action="{{ route('admin.property.photos.store', $property->slug) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2" style="color: var(--color-text-secondary);">
                                Selecciona hasta 30 fotos (JPG, PNG, WEBP - máx. 5MB cada una)
                            </label>
                            <input 
                                type="file" 
                                name="photos[]" 
                                id="photos"
                                multiple
                                accept="image/jpeg,image/png,image/webp"
                                required
                                style="display: none;"
                            >
                            <button type="button" onclick="document.getElementById('photos').click()" class="btn-action btn-action-secondary sn-sentence" style="height: 36px;">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span id="file-label">Elegir archivos</span>
                            </button>
                            <span id="file-count" style="color: var(--color-text-secondary); font-size: 0.875rem; margin-left: 0.75rem;"></span>
                            @error('photos')
                                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                            @enderror
                            @error('photos.*')
                                <p class="mt-1 text-sm" style="color: #ef4444;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-action btn-action-primary sn-sentence" style="height: 36px;">
                            Subir fotos
                        </button>
                        
                        <script>
                            document.getElementById('photos').addEventListener('change', function() {
                                const count = this.files.length;
                                const label = document.getElementById('file-label');
                                const countSpan = document.getElementById('file-count');
                                if (count > 0) {
                                    label.textContent = count === 1 ? '1 archivo seleccionado' : count + ' archivos seleccionados';
                                    countSpan.textContent = '';
                                } else {
                                    label.textContent = 'Elegir archivos';
                                    countSpan.textContent = '';
                                }
                            });
                        </script>
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
                                            <form method="POST" action="{{ route('admin.property.photos.set-cover', [$property->slug, $photo->id]) }}" class="inline-block">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    title="Marcar como portada"
                                                    class="btn-photo btn-photo-star"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Botón eliminar --}}
                                        <form method="POST" action="{{ route('admin.property.photos.destroy', [$property->slug, $photo->id]) }}" class="inline-block" onsubmit="return confirm('¿Eliminar esta foto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                title="Eliminar foto"
                                                class="btn-photo btn-photo-delete"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
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
                            <strong>Tip:</strong> Arrastra las fotos desde la barra inferior para reordenarlas. Haz clic en ★ para marcar como portada o en el icono de papelera para eliminar.
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
            border-radius: var(--radius-sm);
            text-transform: none;
            letter-spacing: 0;
            font-weight: 600;
            line-height: 1.25rem;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
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
        .admin-file-input {
            padding: 0.75rem;
            border: 1px solid var(--color-border-light);
            border-radius: 2px;
            background-color: rgba(255, 255, 255, 0.03);
        }

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
        }

        .photo-card:hover {
            border: 1px solid var(--color-accent);
        }

        .photo-img {
            width: 100%;
            height: 12rem;
            object-fit: cover;
            display: block;
        }

        .photo-badge {
            position: absolute;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            z-index: 20;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .photo-badge-cover {
            top: 8px;
            left: 8px;
            background-color: var(--color-warning);
            color: #000;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .photo-badge-order {
            bottom: 36px;
            right: 8px;
            background-color: var(--color-bg-primary);
            color: var(--color-text-primary);
            border: 1px solid var(--color-border-light);
            font-size: 13px;
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
            padding: 8px 10px;
            color: white;
            border: none;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-photo svg {
            display: block;
        }

        .btn-photo-star {
            background-color: var(--color-warning);
            color: #000;
        }

        .btn-photo-star:hover {
            opacity: 0.9;
        }

        .btn-photo-delete {
            background-color: var(--color-error);
        }

        .btn-photo-delete:hover {
            opacity: 0.9;
        }

        .drag-handle {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 36px;
            background-color: var(--color-accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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
                    fetch('{{ route("admin.property.photos.reorder", $property->slug) }}', {
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
