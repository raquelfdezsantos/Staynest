<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Staynest') }}</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

    <!-- Scripts (Vite carga Tailwind primero) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Staynest Styles (después para que no sobrescriba responsive de Tailwind) -->
    <link rel="stylesheet" href="{{ asset('css/staynest.css') }}">


</head>

<body>
    {{-- Navegación: admin o pública --}}
    @if(request()->routeIs('admin.*'))
        <x-nav-admin />
    @else
        @php 
            // Detectar la propiedad actual para la navegación
            $navProperty = isset($property) ? $property : null;
            
            // Si no existe, intentar obtenerla desde el slug en la URL
            if (!$navProperty && request()->route('property')) {
                $navProperty = request()->route('property') instanceof \App\Models\Property 
                    ? request()->route('property') 
                    : \App\Models\Property::where('slug', request()->route('property'))->first();
            }
            
            // Preferir siempre la sesión si existe (contexto persistente)
            if (session('current_property_slug')) {
                $sessionProp = \App\Models\Property::where('slug', session('current_property_slug'))
                    ->whereNull('deleted_at')
                    ->first();
                if ($sessionProp) {
                    $navProperty = $sessionProp;
                }
            }

            // NO forzar cambio a primera propiedad si ya hay contexto; solo si absolutamente no hay ninguna disponible
            if (!$navProperty) {
                $navProperty = \App\Models\Property::whereNull('deleted_at')->first();
            }
        @endphp
        <x-nav-public 
            :transparent="request()->routeIs('home') || request()->routeIs('properties.show')" 
            :property="$navProperty" />
    @endif

    <!-- Page Content -->
    {{-- Home sin container (hero full), resto con container --}}
    <main class="{{ request()->routeIs('home') || request()->routeIs('properties.show') ? '' : 'container mt-xl' }}">
        @yield('content')
        {{ $slot ?? '' }}
    </main>


    <!-- Footer -->
    <footer
        style="background-color: var(--color-bg-secondary); border-top: 1px solid var(--color-border-light); margin-top: var(--spacing-2xl);">
        <div class="container" style="padding-top: var(--spacing-xl); padding-bottom: var(--spacing-xl);">
            <div class="footer-grid"
                style="gap:var(--spacing-xl); font-size:var(--text-sm); color:var(--color-text-secondary);">
                {{-- Columna 1: Licencias --}}
                <div class="footer-col-1">
                    <h3
                        style="font-family:var(--font-serif); font-size:var(--text-lg); color:var(--color-text-primary); margin-bottom:var(--spacing-md);">
                        Información Legal</h3>
                    @php 
                        // Intentar obtener la propiedad actual desde la vista
                        $footerProperty = isset($property) ? $property : null;
                        
                        // Si no existe, intentar obtenerla desde el slug en la URL
                        if (!$footerProperty && request()->route('property')) {
                            $footerProperty = request()->route('property') instanceof \App\Models\Property 
                                ? request()->route('property') 
                                : \App\Models\Property::where('slug', request()->route('property'))->first();
                        }
                        
                        // Si estamos en área de admin, obtener la primera propiedad del admin autenticado
                        if (!$footerProperty && request()->routeIs('admin.*') && auth()->check()) {
                            $footerProperty = \App\Models\Property::where('user_id', auth()->id())->first();
                        }
                        
                        // Si aún no hay propiedad, usar la primera disponible
                        if (!$footerProperty) {
                            $footerProperty = \App\Models\Property::first();
                        }
                    @endphp
                    @if($footerProperty && ($footerProperty->tourism_license || $footerProperty->rental_registration))
                        @if($footerProperty->tourism_license)
                            <p style="margin-bottom:var(--spacing-sm);">
                                <span style="font-weight:500;">Asturias — Registro autonómico</span><br>
                                {{ $footerProperty->tourism_license }}
                            </p>
                        @endif
                        @if($footerProperty->rental_registration)
                            <p>
                                <span style="font-weight:500;">España — Registro nacional</span><br>
                                <span class="reg-nacional">{{ $footerProperty->rental_registration }}</span>
                            </p>
                        @endif
                    @endif
                </div>

                {{-- Columna 2: Enlaces legales --}}
                <div class="footer-col-2">
                    <h3
                        style="font-family:var(--font-serif); font-size:var(--text-lg); color:var(--color-text-primary); margin-bottom:var(--spacing-md);">
                        Legal</h3>
                    <ul style="list-style:none; padding:0;">
                        <li style="margin-bottom:var(--spacing-xs);"><a href="{{ route('legal.aviso') }}"
                                class="sn-link">Aviso Legal</a></li>
                        <li style="margin-bottom:var(--spacing-xs);"><a href="{{ route('legal.privacidad') }}"
                                class="sn-link">Política de Privacidad</a></li>
                        <li><a href="{{ route('legal.cookies') }}" class="sn-link">Política de Cookies</a></li>
                    </ul>
                </div>

                {{-- Columna 3: Navegación --}}
                <div class="footer-col-3">
                    <div class="footer-col-3-inner">
                        <h3
                            style="font-family:var(--font-serif); font-size:var(--text-lg); color:var(--color-text-primary); margin-bottom:var(--spacing-md);">
                            Staynest</h3>
                        <ul style="list-style:none; padding:0;">
                            <li style="margin-bottom:var(--spacing-xs);">
                                <a href="{{ route('discover') }}" class="sn-link">Descubre Staynest</a>
                            </li>
                            <li style="margin-bottom:var(--spacing-xs);">
                                <a href="{{ route('soporte.index') }}" class="sn-link">Soporte</a>
                            </li>
                        </ul>
                        @if(isset($footerProperty) && $footerProperty)
                            <p style="color:var(--color-text-secondary); margin-top:var(--spacing-md);">&copy; {{ date('Y') }} Todos los derechos <span class="break-here">reservados.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Crédito --}}
            <div class="footer-credit-block">
                <p style="font-size:var(--text-xs); color:var(--color-text-muted);">
                    Desarrollado por <span style="font-weight:500; color:var(--color-text-secondary);">Raquel Fernández
                        Santos</span> ·
                    <span style="font-weight:600; color:var(--color-accent);">{{ config('app.name') }}</span>
                </p>
                <p class="footer-cookie-row footer-credit"
                    style="font-size:var(--text-xs); color:var(--color-text-muted); margin-top:var(--spacing-sm);">
                    <span class="footer-cookie-inner">
                        <x-icon name="cookie" :size="16" class="footer-cookie-icon" />
                        <span>
                            Este sitio utiliza cookies técnicas necesarias.
                            <a href="{{ route('legal.cookies') }}" class="sn-link">
                                Más información
                            </a>
                        </span>
                    </span>
                </p>



            </div>
        </div>
    </footer>

    @stack('scripts')
    @yield('scripts')

</body>

</html>