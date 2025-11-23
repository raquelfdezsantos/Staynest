<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Staynest') }} – Acceso</title>
    <link rel="stylesheet" href="{{ asset('css/staynest.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php 
        $navProperty = null;
        // Intentar obtener por slug en ruta (p.e. /propiedades/{property}/login)
        if (request()->route('property')) {
            $navProperty = request()->route('property') instanceof \App\Models\Property
                ? request()->route('property')
                : \App\Models\Property::where('slug', request()->route('property'))
                    ->whereNull('deleted_at')
                    ->first();
        }
        // Usar propiedad de sesión si existe
        if (session('current_property_slug')) {
            $sessionProp = \App\Models\Property::where('slug', session('current_property_slug'))
                ->whereNull('deleted_at')
                ->first();
            if ($sessionProp) { $navProperty = $sessionProp; }
        }
        // Fallback: primera disponible si nada encontrado
        if (!$navProperty) {
            $navProperty = \App\Models\Property::whereNull('deleted_at')->first();
        }
    @endphp
    <x-nav-public :property="$navProperty" />
    
    <main class="container mt-xl">
        @if(request()->routeIs('register') || request()->routeIs('register.client') || request()->routeIs('register.admin'))
        <div class="sn-reservar" style="max-width: 64rem; margin: 0 auto; padding: 2.5rem 1rem;">
            {{ $slot }}
        </div>
        @else
        <div class="sn-reservar" style="max-width: 28rem; margin: 0 auto; padding: 2.5rem 1rem;">
            <header class="mb-10 text-center">
                <h1 class="text-4xl font-serif mb-4">Iniciar sesión</h1>
                <p class="text-neutral-300" style="max-width:32rem; margin:0 auto;">Accede a tu cuenta para gestionar reservas y pagos.</p>
            </header>
            {{ $slot }}
            <footer style="margin-top: var(--spacing-lg); text-align:center; font-size: var(--text-xs); color: var(--color-text-muted); padding-top: var(--spacing-lg);">
                &copy; {{ date('Y') }} {{ config('app.name') }} · Acceso seguro · <a href="{{ route('legal.cookies') }}" class="sn-link" style="font-size: var(--text-xs);">Cookies</a>
            </footer>
        </div>
        @endif
    </main>
</body>
</html>
