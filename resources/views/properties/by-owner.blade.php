@extends('layouts.app')

@section('title', 'Propiedades de ' . $ownerName)

@section('content')
<div class="sn-reservar max-w-5xl mx-auto px-4 py-10">
    {{-- Header centrado con estilo Staynest --}}
    <header class="mb-16 text-center">
        <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">
            Propiedades de {{ $ownerName }}
        </h1>
        <p class="text-lg" style="color: var(--color-text-secondary);">
            Explora todos los alojamientos disponibles
        </p>
    </header>

    @if($properties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <a href="{{ route('properties.show', $property->slug) }}" class="group">
                    <div class="bg-neutral-800 overflow-hidden transition-all duration-300 h-full flex flex-col" 
                         style="border-radius: var(--radius-base); border: 1px solid transparent;" 
                         onmouseenter="this.style.borderColor='var(--color-accent)'" 
                         onmouseleave="this.style.borderColor='transparent'">
                        
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
                                <div class="mt-auto pt-4 border-t border-neutral-700">
                                    <p class="text-xs text-neutral-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Capacidad: {{ $property->capacity }} personas
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
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" 
                 style="color: var(--color-text-tertiary); margin: 0 auto 1rem;">
                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <p style="color: var(--color-text-secondary); font-size: 1.125rem;">
                No hay propiedades disponibles
            </p>
        </div>
    @endif
</div>
@endsection
