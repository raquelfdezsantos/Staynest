@extends('layouts.app')

@section('title', 'Descubre Staynest')

@section('content')
<div class="container py-12">
    <div class="max-w-7xl mx-auto">
        {{-- Hero Section --}}
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Descubre <span class="text-blue-600 dark:text-blue-400">Staynest</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                La plataforma que conecta propietarios y huéspedes sin intermediarios ni comisiones
            </p>
        </div>

        {{-- Cómo funciona --}}
        <div class="mb-20">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                ¿Cómo funciona Staynest?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                {{-- Para Propietarios --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 border-t-4 border-blue-600">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Para Propietarios</h3>
                    <ul class="space-y-3 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Sin comisiones por reserva</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Cuota fija de mantenimiento</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Control total de tu propiedad</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>URL única para tu alojamiento</span>
                        </li>
                    </ul>
                </div>

                {{-- Modelo de Negocio --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 border-t-4 border-green-600">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Transparencia Total</h3>
                    <div class="space-y-4 text-gray-600 dark:text-gray-400">
                        <p>Las OTAs tradicionales cobran entre un <strong class="text-red-600 dark:text-red-400">15-20%</strong> de comisión por cada reserva.</p>
                        <p>Con Staynest pagas una <strong class="text-green-600 dark:text-green-400">cuota fija</strong> y mantienes el 100% de tus ingresos.</p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mt-4">
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-300">
                                💡 Ejemplo: En una reserva de 1.000€, ahorras hasta 200€ en comisiones
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Para Huéspedes --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 border-t-4 border-purple-600">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Para Huéspedes</h3>
                    <ul class="space-y-3 text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Contacto directo con el propietario</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Sin intermediarios</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Experiencia personalizada</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Reservas seguras y confiables</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Propiedades Disponibles --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
                Propiedades Disponibles
            </h2>
            
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

                                    @if($property->user)
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Gestionado por <span class="font-medium text-gray-700 dark:text-gray-300">{{ $property->user->name }}</span>
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
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">No hay propiedades disponibles en este momento</p>
                </div>
            @endif
        </div>

        {{-- CTA Section --}}
        <div class="mt-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-2xl p-12 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">¿Tienes un alojamiento turístico?</h2>
            <p class="text-xl mb-8 opacity-90">Únete a Staynest y empieza a ahorrar en comisiones</p>
            <a href="{{ route('register.admin') }}" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg">
                Registra tu propiedad
            </a>
        </div>
    </div>
</div>
@endsection
