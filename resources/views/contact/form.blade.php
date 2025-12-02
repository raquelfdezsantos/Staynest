@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
    <div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-serif mb-3" style="font-weight:400;">Contacto</h1>
            <p class="text-neutral-300 max-w-2xl mx-auto whitespace-normal md:whitespace-nowrap">
                ¿Dudas, consultas o disponibilidad especial? Escríbenos y te responderemos lo antes posible.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 items-stretch">
            <!-- Formulario (estrecho) -->
            <div id="contact-form-column"
                 class="md:col-span-2 lg:col-span-2 space-y-6 max-w-xl flex flex-col">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Revisa los siguientes campos:</strong>
                        <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('properties.contact.store', $property) }}"
                      class="space-y-4"
                      style="display:flex;flex-direction:column;"
                      novalidate>
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name"
                                      name="name"
                                      class="block mt-1 w-full"
                                      :value="old('name')"
                                      autofocus />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email"
                                      type="email"
                                      name="email"
                                      class="block mt-1 w-full"
                                      :value="old('email')" />
                    </div>

                    <div>
                        <x-input-label for="subject" value="Asunto" />
                        <x-text-input id="subject"
                                      name="subject"
                                      class="block mt-1 w-full"
                                      :value="old('subject')" />
                    </div>

                    <div>
                        <x-input-label for="message" value="Mensaje" />
                        <textarea id="message"
                                  name="message"
                                  rows="6"
                                  class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400">{{ old('message') }}</textarea>
                    </div>

                    <div style="align-self:flex-start; margin-top:.5rem;">
                        <x-primary-button>Enviar</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- MAPA --}}
            @php
                $hasCoords = !empty($property->latitude) && !empty($property->longitude);
                $lat = $hasCoords ? (float) $property->latitude : 43.545;
                $lng = $hasCoords ? (float) $property->longitude : -5.661;

                $fullAddress = collect([
                    $property->address,
                    $property->postal_code,
                    $property->city,
                    $property->province,
                    'España',
                ])->filter()->implode(', ');
            @endphp

            <div class="md:col-span-2 lg:col-span-2 space-y-4">
                <h2 class="font-serif text-xl mb-2" style="font-weight:500;">Dónde estamos</h2>
                <div class="text-sm text-neutral-300 leading-relaxed">
                    {{ optional($property)->address ?? 'Dirección no disponible' }}
                    @if(!empty(optional($property)->postal_code)) · {{ optional($property)->postal_code }}@endif
                    @if(!empty(optional($property)->city)) · {{ optional($property)->city }}@endif
                    @if(!empty(optional($property)->province)) ({{ optional($property)->province }})@endif
                </div>

                <div id="map-contact" class="w-full"
                     style="height:460px;border:1px solid var(--color-border-light);border-radius:var(--radius-base);overflow:hidden;"></div>
            </div>

            @pushOnce('scripts')
                <script>
                    function initContactMap() {
                        const mapEl = document.getElementById('map-contact');
                        if (!mapEl || !window.google || !google.maps) return;

                        const root = document.documentElement;
                        const isDark =
                            root.getAttribute('data-theme') === 'dark' ||
                            root.classList.contains('dark');

                        // Map IDs desde config/services.php
                        const mapIdLight = @json(config('services.google_maps.map_id') ?? config('services.google_maps.map_id_light'));
                        const mapIdDark  = @json(config('services.google_maps.map_id_dark'));

                        const mapIdToUse = (isDark && mapIdDark) ? mapIdDark : mapIdLight;

                        const baseLoc = { lat: {{ $lat }}, lng: {{ $lng }} };

                        @if($hasCoords)
                            // ===== CON COORDENADAS =====
                            const loc = baseLoc;

                            const mapOptions = {
                                center: loc,
                                zoom: 16,
                            };

                            if (mapIdToUse) {
                                mapOptions.mapId = mapIdToUse;
                            }

                            const map = new google.maps.Map(mapEl, mapOptions);

                            if (google.maps.marker?.AdvancedMarkerElement) {
                                new google.maps.marker.AdvancedMarkerElement({
                                    map,
                                    position: loc,
                                    title: @json($property->name ?? 'Staynest'),
                                });
                            } else {
                                new google.maps.Marker({
                                    map,
                                    position: loc,
                                    title: @json($property->name ?? 'Staynest'),
                                });
                            }

                            // Sincronizar altura del mapa
                            requestAnimationFrame(() => {
                                const syncMapHeight = () => {
                                    const ta = document.getElementById('message');
                                    if (!ta) return;
                                    const taRect = ta.getBoundingClientRect();
                                    const mapRect = mapEl.getBoundingClientRect();
                                    const desired = Math.max(260, Math.round(taRect.bottom - mapRect.top));
                                    mapEl.style.height = desired + 'px';
                                };
                                syncMapHeight();
                                window.addEventListener('resize', () => requestAnimationFrame(syncMapHeight));
                            });

                        @else
                            // ===== SIN COORDENADAS → GEOCODER =====
                            const address = @json($fullAddress);
                            const geocoder = new google.maps.Geocoder();

                            geocoder.geocode({ address }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    const loc = results[0].geometry.location;

                                    const mapOptions = {
                                        center: loc,
                                        zoom: 15,
                                    };

                                    if (mapIdToUse) {
                                        mapOptions.mapId = mapIdToUse;
                                    }

                                    const map = new google.maps.Map(mapEl, mapOptions);

                                    if (google.maps.marker?.AdvancedMarkerElement) {
                                        new google.maps.marker.AdvancedMarkerElement({
                                            map,
                                            position: loc,
                                            title: @json($property->name ?? 'Staynest'),
                                        });
                                    } else {
                                        new google.maps.Marker({
                                            map,
                                            position: loc,
                                            title: @json($property->name ?? 'Staynest'),
                                        });
                                    }

                                    // Sincronizar altura del mapa
                                    requestAnimationFrame(() => {
                                        const syncMapHeight = () => {
                                            const ta = document.getElementById('message');
                                            if (!ta) return;
                                            const taRect = ta.getBoundingClientRect();
                                            const mapRect = mapEl.getBoundingClientRect();
                                            const desired = Math.max(260, Math.round(taRect.bottom - mapRect.top));
                                            mapEl.style.height = desired + 'px';
                                        };
                                        syncMapHeight();
                                        window.addEventListener('resize', () => requestAnimationFrame(syncMapHeight));
                                    });
                                } else {
                                    mapEl.innerHTML = 'No se pudo mostrar el mapa.';
                                }
                            });
                        @endif
                    }

                    (function loadGoogleMapsForContact() {
                        if (window.google && window.google.maps) {
                            initContactMap();
                            return;
                        }

                        const params = new URLSearchParams({
                            key: "{{ config('services.google_maps.api_key') }}",
                            v: "beta",
                            libraries: "marker",
                            callback: "initContactMap",
                            loading: "async",
                            map_ids: "{{ config('services.google_maps.map_id') ?? config('services.google_maps.map_id_light') }},{{ config('services.google_maps.map_id_dark') }}",
                        });

                        const s = document.createElement('script');
                        s.src = "https://maps.googleapis.com/maps/api/js?" + params.toString();
                        s.async = true;
                        s.defer = true;
                        document.head.appendChild(s);
                    })();
                </script>
            @endPushOnce
        </div>
    </div>
@endsection
