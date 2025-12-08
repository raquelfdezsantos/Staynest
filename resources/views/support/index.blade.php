@extends('layouts.app')

@section('title', 'Soporte')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    {{-- Header centrado --}}
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">
            Soporte <span style="color: var(--color-accent);">Staynest</span>
        </h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">
            ¿Necesitas ayuda? Contáctanos y te responderemos lo antes posible.
        </p>
    </header>

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <strong>Revisa los siguientes campos:</strong>
                <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('soporte.store', [], false) }}" style="display: flex; flex-direction: column; gap: 1.5rem;" novalidate>
            @csrf
            @if(session('current_property_slug'))
                <input type="hidden" name="property" value="{{ session('current_property_slug') }}">
            @endif

                {{-- Nombre --}}
                <div>
                    <x-input-label for="name" value="Nombre" />
                    <x-text-input 
                        id="name"
                        name="name" 
                        class="block mt-1 w-full"
                        :value="old('name', auth()->user()->name ?? '')"
                        autofocus 
                    />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input 
                        id="email"
                        type="email"
                        name="email" 
                        class="block mt-1 w-full"
                        :value="old('email', auth()->user()->email ?? '')"
                    />
                </div>

                {{-- Asunto --}}
                <div>
                    <x-input-label for="subject" value="Asunto" />
                    <x-text-input 
                        id="subject"
                        name="subject" 
                        class="block mt-1 w-full"
                        :value="old('subject')"
                    />
                </div>

                {{-- Mensaje --}}
                <div>
                    <x-input-label for="message" value="Mensaje" />
                    <textarea id="message"
                              name="message"
                              rows="6"
                              class="sn-input w-full bg-neutral-800 border border-neutral-700 rounded px-3 py-2 text-neutral-100 shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-0 focus:ring-[color:var(--color-accent)] focus:border-[color:var(--color-accent)] placeholder:text-neutral-400"
                              placeholder="Describe tu consulta o problema...">{{ old('message') }}</textarea>
                </div>

            {{-- Botón --}}
            <div style="align-self: flex-start; margin-top: 0.5rem;">
                <x-primary-button>Enviar</x-primary-button>
            </div>
        </form>
</div>
@endsection
