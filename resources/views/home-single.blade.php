@extends('layouts.app')

@section('title', ($property->name ?? 'Staynest') . ' – Staynest')

@php $transparentHeader = true; @endphp

@section('content')
    @php
        // Fotos ordenadas; cogemos la primera para el hero o usamos fallback
        $photos = ($property?->photos?->sortBy('sort_order')) ?? collect();
        $first  = $photos->first();
        
        // Detectar si es una ruta pública (images/) o de storage
        if ($first && !empty($first->url)) {
            if (str_starts_with($first->url, 'http')) {
                $hero = $first->url;
            } elseif (str_starts_with($first->url, 'images/')) {
                $hero = asset($first->url);
            } else {
                $hero = asset('storage/' . ltrim($first->url, '/'));
            }
        } else {
            $hero = 'https://picsum.photos/1600/900';
        }
        
        // Mostrar todas las fotos restantes (hasta 30) en la galería
        $morePhotos = $photos->slice(1);
    @endphp

    {{-- HERO principal: flush top bajo header transparente (clase sn-hero--flush-top) --}}
    <section class="sn-hero sn-hero--flush-top" style="--hero-img: url('{{ $hero ?? 'https://picsum.photos/1600/900' }}')">

        <div class="sn-hero__overlay"></div>

        <div class="container sn-hero__content">
            <div style="margin-top: 50px;">
                <h1 class="sn-hero__title">{{ $property->name ?? 'Staynest' }}</h1>
                <div class="sn-hero__strap">
                    {{ $property->short_tagline ?? 'Tu escapada perfecta, todo el año.' }}
                </div>
            </div>
        </div>

        @if($property && ($property->tourism_license || $property->rental_registration))
            <div class="sn-hero__panel-wrap" style="margin-top: 50px;">
                <div class="sn-hero__panel">
                    <div>
                        <small>Asturias — Registro autonómico</small>
                        <strong>{{ $property->tourism_license ?? '—' }}</strong>
                    </div>
                    <div>
                        <small>España — Registro nacional</small>
                        <strong class="hero-reg-nacional">{{ $property->rental_registration ?? '—' }}</strong>
                    </div>
                </div>
            </div>
        @endif

        <div class="sn-hero__blend"></div>
    </section>

    {{-- Contenedor igual que Entorno, Contacto y Reservar --}}
    <div class="container" style="padding-top: calc(var(--spacing-xl) * 3); padding-bottom: var(--spacing-xl);">
        <div class="max-w-5xl mx-auto px-4">
            
            {{-- DESCRIPCIÓN --}}
            <section style="margin-bottom: var(--spacing-xl);">
                <article style="color: var(--color-text-secondary); line-height:1.7;">
                    {!! nl2br(e($property->description ?? 'Alojamiento acogedor y minimalista.')) !!}
                </article>
            </section>

            {{-- CAPACIDAD --}}
            <section style="margin-bottom: var(--spacing-xl); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span style="color: var(--color-text-primary); font-size: var(--text-base);">Capacidad: <strong>{{ $property->capacity }} {{ $property->capacity == 1 ? 'persona' : 'personas' }}</strong></span>
                </div>
            </section>

            {{-- SERVICIOS --}}
            @php
                $services = $property->services ?? [];
                $serviceLabels = [
                    'wifi' => 'WiFi',
                    'parking' => 'Parking',
                    'pool' => 'Piscina',
                    'washer' => 'Lavadora',
                    'dishwasher' => 'Lavavajillas',
                    'heating' => 'Calefacción',
                    'air_conditioning' => 'Aire acondicionado',
                    'hairdryer' => 'Secador',
                    'first_aid_kit' => 'Botiquín',
                    'pets_allowed' => 'Mascotas permitidas',
                    'smoking_allowed' => 'Permitido fumar',
                    'tv' => 'TV / Smart TV',
                    'kitchen' => 'Cocina equipada',
                    'towels' => 'Toallas incluidas',
                    'bed_linen' => 'Ropa de cama incluida',
                    'terrace' => 'Terraza / Balcón',
                    'elevator' => 'Ascensor',
                    'crib' => 'Cuna disponible',
                ];
            @endphp
@if(is_array($services) && count($services) > 0)
                <section style="margin-bottom: var(--spacing-xl);">
                    <h2 style="font-size: var(--text-xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 1rem;">Servicios</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                        @foreach($services as $service)
                            @if(isset($serviceLabels[$service]))
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-secondary); font-size: var(--text-sm);">
                                    @php
                                        $icons = [
                                            'wifi' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>',
                                            'parking' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
                                            'pool' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>',
                                            'washer' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="13" r="5" stroke-width="2"></circle><path stroke-linecap="round" stroke-width="2" d="M4 3h16v18H4z"></path><circle cx="7" cy="6" r="1" fill="currentColor"></circle><circle cx="10" cy="6" r="1" fill="currentColor"></circle></svg>',
                                            'dishwasher' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"></rect><path stroke-linecap="round" stroke-width="2" d="M7 7h10M7 11h6"></path><circle cx="12" cy="16" r="2.5" stroke-width="2"></circle></svg>',
                                            'heating' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>',
                                            'air_conditioning' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>',
                                            'hairdryer' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                                            'first_aid_kit' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                                            'pets_allowed' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                            'smoking_allowed' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>',
                                            'tv' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
                                            'kitchen' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
                                            'towels' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>',
                                            'bed_linen' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
                                            'terrace' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>',
                                            'elevator' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>',
                                            'crib' => '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                        ];
                                    @endphp
                                    {!! $icons[$service] ?? '<svg style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' !!}
                                    {{ $serviceLabels[$service] }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- GALERÍA compacta (sin B/N) --}}
            @if(($morePhotos ?? collect())->count() > 0)
                <section style="margin-top: calc(var(--spacing-xl) * 2);">
                    <div class="sn-gallery-compact"
                         style="display:grid; gap:10px; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));">
                        @foreach(($morePhotos ?? collect()) as $p)
                            @php
                                // Detectar si es HTTP, ruta pública (images/) o storage
                                if (!empty($p->url) && str_starts_with($p->url, 'http')) {
                                    $src = $p->url;
                                } elseif (!empty($p->url) && str_starts_with($p->url, 'images/')) {
                                    $src = asset($p->url);
                                } else {
                                    $src = asset('storage/' . ltrim($p->url ?? '', '/'));
                                }
                                $w = $p->width ?? 1600;
                                $h = $p->height ?? 1067;
                            @endphp
                            <a href="{{ $src }}" data-pswp-width="{{ $w }}" data-pswp-height="{{ $h }}">
                                <img src="{{ $src }}" alt="Foto {{ $loop->iteration }}" loading="lazy"
                                     style="width:100%; height:160px; object-fit:cover; border-radius: var(--radius-base);">
                            </a>
                        @endforeach
                    </div>
                    <div style="text-align:center; margin-top:1rem;">
                        <button type="button" class="inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] text-white font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150" style="border-radius: 2px;"
                                onclick="document.querySelector('.sn-gallery-compact a')?.click()">
                            Ver galería
                        </button>
                    </div>
                </section>
            @endif
            
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Actualizar dimensiones reales de las imágenes para PhotoSwipe
document.addEventListener('DOMContentLoaded', function() {
    const galleryLinks = document.querySelectorAll('.sn-gallery-compact a');
    
    galleryLinks.forEach(link => {
        const img = link.querySelector('img');
        if (img) {
            // Esperar a que la imagen se cargue
            if (img.complete) {
                updateDimensions(img, link);
            } else {
                img.addEventListener('load', function() {
                    updateDimensions(img, link);
                });
            }
        }
    });
    
    function updateDimensions(img, link) {
        // Crear una imagen temporal para obtener dimensiones reales
        const tempImg = new Image();
        tempImg.src = img.src;
        tempImg.onload = function() {
            link.setAttribute('data-pswp-width', this.naturalWidth);
            link.setAttribute('data-pswp-height', this.naturalHeight);
        };
    }
});
</script>
@endpush

