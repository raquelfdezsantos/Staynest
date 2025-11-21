<x-guest-layout>
    <div class="register-choice max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-serif mb-4" style="color: var(--color-text-primary);">Crear cuenta</h1>
            <p style="color: var(--color-text-secondary);">¿Qué tipo de cuenta deseas crear?</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('register.client') }}" class="choice-card">
                <div class="choice-icon">👤</div>
                <div class="choice-content">
                    <h3 class="choice-title">Cuenta de Cliente</h3>
                    <p class="choice-description">Para reservar alojamientos y gestionar tus estancias.</p>
                </div>
            </a>

            <a href="{{ route('register.admin') }}" class="choice-card">
                <div class="choice-icon">🏠</div>
                <div class="choice-content">
                    <h3 class="choice-title">Cuenta de Administrador</h3>
                    <p class="choice-description">Para gestionar un alojamiento y administrar reservas.</p>
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
            display: block;
            background: var(--color-bg-card);
            border: 1px solid var(--color-border-light);
            border-radius: var(--radius-base);
            padding: 1.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        .choice-card:hover {
            border-color: var(--color-accent);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .choice-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .choice-content {
            text-align: center;
        }

        .choice-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--color-text-primary);
            margin-bottom: 0.5rem;
        }

        .choice-description {
            font-size: var(--text-sm);
            color: var(--color-text-secondary);
        }

        /* Light mode */
        html[data-theme="light"] .choice-card {
            background: #fff;
        }
    </style>
</x-guest-layout>