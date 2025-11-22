<x-guest-layout>
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-serif mb-4">Crear cuenta</h1>
        <p class="text-neutral-300 max-w-2xl mx-auto">¿Qué tipo de cuenta deseas crear?</p>
    </header>
    <div class="register-choice">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('register.client') }}" class="choice-card">
                <div class="choice-icon">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-accent);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="choice-content">
                    <h3 class="choice-title">Cuenta de Huésped</h3>
                    <p class="choice-description">Para reservar alojamientos y gestionar tus estancias.</p>
                </div>
            </a>

            <a href="{{ route('register.admin') }}" class="choice-card">
                <div class="choice-icon">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--color-accent);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="choice-content">
                    <h3 class="choice-title">Cuenta de Propietario</h3>
                    <p class="choice-description">Para gestionar tu alojamiento y administrar reservas.</p>
                </div>
            </a>
        </div>

        <div class="text-center mt-8">
            <p style="color: var(--color-text-secondary);">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" style="color: var(--color-accent);" class="hover:underline">Inicia sesión</a>
            </p>
        </div>
    </div>

    <style>
        .choice-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgb(38, 38, 38);
            border: 1px solid transparent;
            border-radius: var(--radius-base);
            padding: 2rem 3.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            height: 100%;
        }

        .choice-card:hover {
            border-color: var(--color-accent);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .choice-icon {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .choice-content {
            text-align: center;
        }

        .choice-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--color-text-primary);
            margin-bottom: 0.75rem;
        }

        .choice-description {
            font-size: var(--text-sm);
            color: var(--color-text-secondary);
            line-height: 1.5;
        }
    </style>
</x-guest-layout>