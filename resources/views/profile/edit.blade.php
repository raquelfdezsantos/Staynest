<x-app-layout>
    <div class="max-w-5xl mx-auto px-4" style="padding-top: var(--spacing-2xl); padding-bottom: var(--spacing-2xl);">
        
        {{-- Header simple --}}
        <header style="margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Mi perfil
            </h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">
                Actualiza tu información personal y configuración de la cuenta.
            </p>
        </header>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            {{-- Información del perfil --}}
            <div style="padding: 2rem; border: 1px solid var(--color-border-light); border-radius: var(--radius-base);">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Cambiar contraseña --}}
            <div style="padding: 2rem; border: 1px solid var(--color-border-light); border-radius: var(--radius-base);">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Eliminar cuenta --}}
            <div style="padding: 2rem; border: 1px solid var(--color-border-light); border-radius: var(--radius-base);">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
