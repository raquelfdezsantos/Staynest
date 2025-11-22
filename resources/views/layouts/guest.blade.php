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
    <x-nav-public />
    
    <main class="container mt-xl">
        @if(request()->routeIs('register'))
        <div class="sn-reservar" style="max-width: 64rem; margin: 0 auto; padding: 2.5rem 1rem;">
            {{ $slot }}
        </div>
        @else
        <div class="sn-reservar" style="max-width: 28rem; margin: 0 auto; padding: 2.5rem 1rem;">
            <header class="mb-10 text-center">
                <h1 class="text-4xl font-serif mb-4">Acceso</h1>
                <p class="text-neutral-300">Inicia sesión para gestionar tu estancia</p>
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
