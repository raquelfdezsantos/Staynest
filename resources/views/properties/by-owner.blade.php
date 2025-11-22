@extends('layouts.app')

@section('title', 'Propiedades de ' . $ownerName)

@section('content')
<div class="container py-12">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Propiedades de {{ $ownerName }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Explora todos los alojamientos disponibles</p>
        </div>

        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    <a href="{{ route('properties.show', $property->slug) }}" class="group block">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-300 group-hover:scale-105">
                            @if($property->photos->isNotEmpty())
                                <div class="aspect-video relative overflow-hidden">
                                    <img src="{{ Storage::disk('public')->url($property->photos->first()->path) }}" 
                                         alt="{{ $property->name }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </div>
                            @else
                                <div class="aspect-video bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $property->name }}
                                </h3>
                                
                                @if($property->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                        {{ Str::limit($property->description, 120) }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-4">
                                        @if($property->num_bedrooms)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                </svg>
                                                {{ $property->num_bedrooms }} hab.
                                            </span>
                                        @endif
                                        @if($property->max_capacity)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                {{ $property->max_capacity }} pers.
                                            </span>
                                        @endif
                                    </div>
                                    @if($property->base_price)
                                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                                            {{ number_format($property->base_price, 0, ',', '.') }}€<span class="text-sm font-normal">/noche</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-lg">No hay propiedades disponibles</p>
            </div>
        @endif
    </div>
</div>
@endsection
