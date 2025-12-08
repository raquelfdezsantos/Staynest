<x-app-layout>
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10 admin-slim-badges">
        
        {{-- Header centrado como en Dashboard --}}
        <header class="mb-10 text-center">
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
            <div class="admin-form-card mb-6" style="border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); overflow: hidden;">
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
            <div class="admin-form-card" style="border: 1px solid rgba(var(--color-border-rgb), 0.1); border-radius: var(--radius-base); overflow: hidden;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="font-family: var(--font-sans); color: var(--color-text-primary);">Galería ({{ $photos->count() }} fotos)</h3>

                    @if($photos->isEmpty())
                        <p class="text-center py-8" style="color: var(--color-text-secondary);">No hay fotos subidas. Sube la primera foto usando el formulario de arriba.</p>
                    @else
                        <div id="photos-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($photos as $photo)
                                <div class="photo-item photo-card" data-photo-id="{{ $photo->id }}">
                                    {{-- Imagen --}}
                                    @php
                                        if (str_starts_with($photo->url, 'http')) {
                                            $photoSrc = $photo->url;
                                        } elseif (str_starts_with($photo->url, 'images/')) {
                                            $photoSrc = asset($photo->url);
                                        } else {
                                            $photoSrc = asset('storage/' . $photo->url);
                                        }
                                    @endphp
                                    <img 
                                        src="{{ $photoSrc }}" 
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
                                            <form method="POST" action="{{ route('admin.property.photos.set-cover', [$property->slug, $photo->id]) }}" class="inline-block" id="form-star-{{ $photo->id }}">
                                                @csrf
                                                <button 
                                                    type="submit"
                                                    title="Marcar como portada"
                                                    class="btn-photo btn-photo-star"
                                                    onclick="console.log('Click en estrella foto {{ $photo->id }}'); return true;"
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
            position: relative;
            z-index: 1;
        }
        
        /* Responsive: imágenes más grandes en móviles */
        @media (max-width: 768px) {
            .photo-img {
                height: 16rem; /* 256px - más espacio en móvil */
            }
            .photo-card {
                min-height: 320px;
            }
        }
        
        @media (max-width: 480px) {
            .photo-img {
                height: 18rem; /* 288px - aún más espacio en móviles pequeños */
            }
            .photo-card {
                min-height: 360px;
            }
        }

        .photo-badge {
            position: absolute;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: var(--radius-sm);
            backdrop-filter: blur(8px);
            z-index: 10;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            pointer-events: none;
        }

        .photo-badge-cover {
            top: 8px;
            left: 8px;
            background-color: rgba(var(--color-accent-rgb), 0.9);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .photo-badge-order {
            bottom: 36px;
            right: 8px;
            background-color: rgba(var(--color-bg-primary-rgb), 0.85);
            color: var(--color-text-secondary);
            border: 1px solid rgba(var(--color-border-rgb), 0.3);
            font-size: 12px;
        }

        .photo-actions {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            gap: 6px;
            z-index: 20;
        }
        
        .photo-actions form {
            display: inline-block;
        }

        .btn-photo {
            width: 36px;
            height: 36px;
            border: 1px solid rgba(var(--color-border-rgb), 0.3);
            border-radius: var(--radius-sm);
            backdrop-filter: blur(8px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-photo svg {
            display: block;
            width: 18px;
            height: 18px;
        }

        .btn-photo-star {
            background-color: rgba(var(--color-bg-primary-rgb), 0.85);
            color: var(--color-accent);
        }

        .btn-photo-star:hover {
            background-color: rgba(var(--color-accent-rgb), 0.2);
            border-color: var(--color-accent);
        }

        .btn-photo-delete {
            background-color: rgba(var(--color-bg-primary-rgb), 0.85);
            color: var(--color-text-secondary);
        }

        .btn-photo-delete:hover {
            background-color: rgba(var(--color-error-rgb), 0.2);
            border-color: var(--color-error);
            color: var(--color-error);
        }

        .drag-handle {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 32px;
            background-color: rgba(var(--color-bg-secondary-rgb), 0.95);
            backdrop-filter: blur(8px);
            color: var(--color-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            border-top: 1px solid rgba(var(--color-border-rgb), 0.3);
            z-index: 25;
            transition: all 0.2s ease;
        }

        .drag-handle:hover {
            background-color: rgba(var(--color-accent-rgb), 0.15);
            color: var(--color-accent);
            border-top-color: var(--color-accent);
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

        /* Admin form card styling */
        .admin-form-card {
            background: rgba(51, 51, 51, 0.2) !important;
        }

        html[data-theme="light"] .admin-form-card {
            background: #E3E3E3 !important;
        }

        html[data-theme="dark"] .admin-textarea {
            background: #222222 !important;
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
                filter: '.btn-photo, .photo-actions, button, form',
                preventOnFilter: false,
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
