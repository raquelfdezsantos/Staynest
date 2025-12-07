@extends('layouts.app')

@section('title', $property->environment ? 'El Entorno - ' . ($property->environment->title ?? $property->city) : 'El Entorno')

@section('content')
<div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
    
@if($property->environment)
    {{-- CONTENIDO DESDE EL MODELO PropertyEnvironment --}}
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">{{ $property->environment->title ?? 'El entorno' }}</h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">{{ $property->environment->subtitle ?? 'Descubre el entorno de esta propiedad.' }}</p>
    </header>

    <!-- Sección destacada / hero -->
    <section class="mb-16 grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 aspect-video bg-neutral-800 flex items-center justify-center text-neutral-500 overflow-hidden" style="border-radius:var(--radius-base);">
            @if($property->environment->hero_photo)
                @php
                    if (str_starts_with($property->environment->hero_photo, 'http')) {
                        $heroSrc = $property->environment->hero_photo;
                    } elseif (str_starts_with($property->environment->hero_photo, 'images/')) {
                        $heroSrc = asset($property->environment->hero_photo);
                    } else {
                        $heroSrc = asset('storage/' . $property->environment->hero_photo);
                    }
                @endphp
                <img src="{{ $heroSrc }}" alt="{{ $property->environment->title }}" class="w-full h-full object-cover" />
            @else
                <span class="text-sm">[Foto destacada]</span>
            @endif
        </div>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Tu destino</h2>
            @if($property->environment->summary)
                @php
                    $lines = explode("\n", $property->environment->summary);
                    $intro = '';
                    $listItems = [];
                    
                    foreach ($lines as $line) {
                        $trimmed = trim($line);
                        if ($trimmed) {
                            // Si contiene ":" al final, es texto introductorio
                            if (str_ends_with($trimmed, ':')) {
                                $intro = $trimmed;
                            } else {
                                $listItems[] = $trimmed;
                            }
                        }
                    }
                @endphp
                
                @if($intro)
                    <p class="text-sm text-neutral-300">{{ $intro }}</p>
                @endif
                
                @if(count($listItems) > 0)
                    <ul class="text-sm text-neutral-400 list-disc pl-5 space-y-1">
                        @foreach($listItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            @else
                <p class="text-neutral-300 text-sm">Información sobre el entorno de esta propiedad.</p>
            @endif
        </div>
    </section>

    <!-- Bloques temáticos -->
    <section class="space-y-16">
        <div class="grid md:grid-cols-2 gap-8">
            @if($property->environment->nature_description)
                <div class="flex flex-col" style="height: 100%;">
                    <h3 class="text-lg font-semibold mb-3">Naturaleza</h3>
                    <p class="text-neutral-300 text-sm mb-3 flex-grow">{{ $property->environment->nature_description }}</p>
                    <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs overflow-hidden" style="border-radius:var(--radius-base);">
                        @if($property->environment->nature_photo)
                            @php
                                if (str_starts_with($property->environment->nature_photo, 'http')) {
                                    $natureSrc = $property->environment->nature_photo;
                                } elseif (str_starts_with($property->environment->nature_photo, 'images/')) {
                                    $natureSrc = asset($property->environment->nature_photo);
                                } else {
                                    $natureSrc = asset('storage/' . $property->environment->nature_photo);
                                }
                            @endphp
                            <img src="{{ $natureSrc }}" alt="Naturaleza" class="w-full h-full object-cover" />
                        @else
                            <span class="text-sm">[Foto naturaleza]</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($property->environment->culture_description)
                <div class="flex flex-col" style="height: 100%;">
                    <h3 class="text-lg font-semibold mb-3">Cultura y Patrimonio</h3>
                    <p class="text-neutral-300 text-sm mb-3 flex-grow">{{ $property->environment->culture_description }}</p>
                    <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs overflow-hidden" style="border-radius:var(--radius-base);">
                        @if($property->environment->culture_photo)
                            @php
                                if (str_starts_with($property->environment->culture_photo, 'http')) {
                                    $cultureSrc = $property->environment->culture_photo;
                                } elseif (str_starts_with($property->environment->culture_photo, 'images/')) {
                                    $cultureSrc = asset($property->environment->culture_photo);
                                } else {
                                    $cultureSrc = asset('storage/' . $property->environment->culture_photo);
                                }
                            @endphp
                            <img src="{{ $cultureSrc }}" alt="Cultura" class="w-full h-full object-cover" />
                        @else
                            <span class="text-sm">[Foto cultura]</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            @if($property->environment->activities_description)
                <div class="flex flex-col" style="height: 100%;">
                    <h3 class="text-lg font-semibold mb-3">Actividades</h3>
                    <p class="text-neutral-300 text-sm mb-3 flex-grow">{{ $property->environment->activities_description }}</p>
                    <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs overflow-hidden" style="border-radius:var(--radius-base);">
                        @if($property->environment->activities_photo)
                            @php
                                if (str_starts_with($property->environment->activities_photo, 'http')) {
                                    $activitiesSrc = $property->environment->activities_photo;
                                } elseif (str_starts_with($property->environment->activities_photo, 'images/')) {
                                    $activitiesSrc = asset($property->environment->activities_photo);
                                } else {
                                    $activitiesSrc = asset('storage/' . $property->environment->activities_photo);
                                }
                            @endphp
                            <img src="{{ $activitiesSrc }}" alt="Actividades" class="w-full h-full object-cover" />
                        @else
                            <span class="text-sm">[Foto actividades]</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($property->environment->services_description)
                <div class="flex flex-col" style="height: 100%;">
                    <h3 class="text-lg font-semibold mb-3">Servicios Cercanos</h3>
                    <p class="text-neutral-300 text-sm mb-3 flex-grow">{{ $property->environment->services_description }}</p>
                    <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs overflow-hidden" style="border-radius:var(--radius-base);">
                        @if($property->environment->services_photo)
                            @php
                                if (str_starts_with($property->environment->services_photo, 'http')) {
                                    $servicesSrc = $property->environment->services_photo;
                                } elseif (str_starts_with($property->environment->services_photo, 'images/')) {
                                    $servicesSrc = asset($property->environment->services_photo);
                                } else {
                                    $servicesSrc = asset('storage/' . $property->environment->services_photo);
                                }
                            @endphp
                            <img src="{{ $servicesSrc }}" alt="Servicios" class="w-full h-full object-cover" />
                        @else
                            <span class="text-sm">[Foto servicios]</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA final -->
    <section class="mt-20 text-center">
        <div class="bg-neutral-800 border border-neutral-700 px-4 py-6 w-full" style="border-radius:var(--radius-base); display:block; width:100%;">
            <p class="text-neutral-100 mb-4">¿Listo para disfrutar? Reserva tu estancia y descubre todo lo que ofrece esta ubicación.</p>
            <a href="{{ route('properties.reservar', $property) }}" class="inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150" style="border-radius: 2px; color: #fff !important;">Reservar ahora</a>
        </div>
    </section>

@else
    {{-- VISTA GENÉRICA PARA OTRAS PROPIEDADES --}}
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">El entorno</h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">Descubre el contexto que rodea esta propiedad: naturaleza, cultura, actividades y servicios cercanos. Estamos trabajando en contenido personalizado para cada ubicación.</p>
    </header>

    <!-- Sección destacada / hero -->
    <section class="mb-16 grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 aspect-video bg-neutral-800 flex items-center justify-center text-neutral-500" style="border-radius:var(--radius-base);">
            <span class="text-sm">[Contenido en desarrollo para esta ubicación]</span>
        </div>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Próximamente</h2>
            <p class="text-neutral-300 text-sm leading-relaxed">Pronto dispondrás aquí de información detallada sobre el área: atractivos naturales, patrimonio cultural, actividades recomendadas y servicios cercanos.</p>
            <ul class="text-sm text-neutral-400 list-disc pl-5 space-y-1">
                <li>Playas y naturaleza cercanas</li>
                <li>Rutas y actividades</li>
                <li>Patrimonio cultural</li>
                <li>Servicios esenciales</li>
            </ul>
        </div>
    </section>

    <!-- Bloques temáticos -->
    <section class="space-y-16">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Naturaleza</h3>
                <p class="text-neutral-300 text-sm">Descripción de espacios naturales, paisajes y biodiversidad que caracterizan esta ubicación.</p>
                <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs" style="border-radius:var(--radius-base);">[Contenido en desarrollo]</div>
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Cultura y Patrimonio</h3>
                <p class="text-neutral-300 text-sm">Lugares históricos, arquitectura y tradiciones locales de la zona.</p>
                <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs" style="border-radius:var(--radius-base);">[Contenido en desarrollo]</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Actividades</h3>
                <p class="text-neutral-300 text-sm">Experiencias recomendadas y actividades disponibles en los alrededores.</p>
                <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs" style="border-radius:var(--radius-base);">[Contenido en desarrollo]</div>
            </div>
            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Servicios Cercanos</h3>
                <p class="text-neutral-300 text-sm">Servicios esenciales: supermercados, farmacias, transporte y más.</p>
                <div class="h-40 bg-neutral-800 flex items-center justify-center text-neutral-500 text-xs" style="border-radius:var(--radius-base);">[Contenido en desarrollo]</div>
            </div>
        </div>
    </section>

    <!-- CTA genérico -->
    <section class="mt-20 text-center">
        <div class="bg-neutral-800 border border-neutral-700 px-4 py-6 w-full" style="border-radius:var(--radius-base); display:block; width:100%;">
            <p class="text-neutral-100 mb-4">Mientras trabajamos en el contenido del entorno, puedes reservar esta propiedad y descubrir todo lo que ofrece.</p>
            <a href="{{ route('properties.reservar', $property) }}" class="inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150" style="border-radius: 2px; color: #fff !important;">Reservar ahora</a>
        </div>
    </section>
@endif

</div>
@endsection