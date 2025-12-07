@extends('layouts.app')

@section('title', 'Descubre Staynest')

@section('content')
<div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
    {{-- Hero Section --}}
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">
            Descubre <span style="color: var(--color-accent);">Staynest</span>
        </h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">
            La plataforma que conecta propietarios y huéspedes sin intermediarios ni comisiones
        </p>
    </header>

    {{-- Cómo funciona --}}
    <section class="mb-16">
        <h2 class="text-2xl font-serif mb-8 text-center">¿Cómo funciona Staynest?</h2>
        
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            {{-- Para Propietarios --}}
            <div class="bg-neutral-800 p-6 space-y-4" style="border-radius: var(--radius-base); border-top: 3px solid var(--color-accent);">
                <div class="w-12 h-12 bg-neutral-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold">Para Propietarios</h3>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Sin comisiones por reserva</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Cuota fija de mantenimiento</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Control total de tu propiedad</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>URL única para tu alojamiento</span>
                    </li>
                </ul>
            </div>

            {{-- Modelo de Negocio --}}
            <div class="bg-neutral-800 p-6 space-y-4" style="border-radius: var(--radius-base); border-top: 3px solid var(--color-accent);">
                <div class="w-12 h-12 bg-neutral-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold">Transparencia Total</h3>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>OTAs cobran 15-20% de comisión</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Staynest: cuota fija sin comisiones</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Mantén 100% de tus ingresos</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Ahorro hasta 200€ por cada 1.000€</span>
                    </li>
                </ul>
            </div>

            {{-- Para Huéspedes --}}
            <div class="bg-neutral-800 p-6 space-y-4" style="border-radius: var(--radius-base); border-top: 3px solid var(--color-accent);">
                <div class="w-12 h-12 bg-neutral-700 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" style="color: var(--color-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold">Para Huéspedes</h3>
                <ul class="space-y-2 text-sm text-neutral-300">
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Contacto directo con el propietario</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Sin intermediarios</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Experiencia personalizada</span>
                    </li>
                    <li class="flex items-start">
                        <span style="color: var(--color-accent);" class="mr-2">✓</span>
                        <span>Reservas seguras y confiables</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Propiedades Disponibles --}}
    <section class="mt-16">
        <h2 class="text-2xl font-serif mb-8 text-center">Propiedades Disponibles</h2>
        
        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($properties as $property)
                    <a href="{{ route('properties.show', $property->slug) }}" class="group">
                        <div class="bg-neutral-800 overflow-hidden transition-all duration-300 h-full flex flex-col" style="border-radius: var(--radius-base); border: 1px solid transparent;" onmouseenter="this.style.borderColor='var(--color-accent)'" onmouseleave="this.style.borderColor='transparent'">
                            @if($property->photos->isNotEmpty())
                                <div class="aspect-video relative overflow-hidden">
                                    @php
                                        $firstPhoto = $property->photos->first();
                                        if (str_starts_with($firstPhoto->url, 'http')) {
                                            $photoUrl = $firstPhoto->url;
                                        } elseif (str_starts_with($firstPhoto->url, 'images/')) {
                                            $photoUrl = asset($firstPhoto->url);
                                        } else {
                                            $photoUrl = asset('storage/' . ltrim($firstPhoto->url, '/'));
                                        }
                                    @endphp
                                    <img src="{{ $photoUrl }}" 
                                         alt="{{ $property->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="aspect-video bg-neutral-700 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-lg font-semibold mb-2 group-hover:text-[color:var(--color-accent)] transition-colors">
                                    {{ $property->name }}
                                </h3>
                                
                                @if($property->description)
                                    <p class="text-neutral-300 text-sm mb-4">
                                        {{ Str::limit($property->description, 120) }}
                                    </p>
                                @endif

                                @if($property->city)
                                    <p class="text-xs text-neutral-400 mb-4">
                                        {{ $property->city }}{{ $property->province ? ', ' . $property->province : '' }}
                                    </p>
                                @endif

                                @if($property->capacity)
                                    <p class="text-xs text-neutral-400 mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Capacidad: {{ $property->capacity }} personas
                                    </p>
                                @endif
                                
                                @if($property->user)
                                    <div class="mt-auto pt-4 border-t border-neutral-700">
                                        <p class="text-xs text-neutral-400">
                                            Gestionado por <span style="color: var(--color-text-primary);">{{ $property->user->name }}</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-neutral-300">No hay propiedades disponibles en este momento</p>
            </div>
        @endif
    </section>

    {{-- CTA Section --}}
    <section class="mt-20 bg-neutral-800 border border-neutral-700 px-4 py-6 w-full text-center" style="border-radius:var(--radius-base); display:block;">
        <h2 class="text-2xl font-serif mb-4">¿Tienes un alojamiento turístico?</h2>
        <p class="text-neutral-300 mb-4">Únete a Staynest y empieza a ahorrar en comisiones</p>
        <a href="{{ route('register.admin') }}" class="inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150" style="border-radius: 2px; color: #fff !important;">Registra tu propiedad</a>
    </section>
</div>
@endsection
