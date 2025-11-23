<x-app-layout>
    <style>
        html[data-theme="dark"] .profile-card {
            background: rgba(51, 51, 51, 0.2) !important;
        }
        html[data-theme="light"] .profile-card {
            background: #e3e3e3 !important;
        }
    </style>
    
    <div class="max-w-5xl mx-auto px-4 py-10">
        {{-- Header centrado --}}
        <header class="mb-16 text-center">
            <h1 class="text-4xl font-serif mb-4" style="color: var(--color-text-primary);">Mi perfil</h1>
            <p style="color: var(--color-text-secondary); font-size: var(--text-base);">Actualiza tu información personal y configuración de la cuenta.</p>
        </header>

        <div class="grid gap-8">
            {{-- Información del perfil --}}
            <section class="profile-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Información del perfil</h2>
                @include('profile.partials.update-profile-information-form')
            </section>

            {{-- Cambiar contraseña --}}
            <section class="profile-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Cambiar contraseña</h2>
                @include('profile.partials.update-password-form')
            </section>

            {{-- Eliminar cuenta --}}
            <section class="profile-card p-6" style="border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.8); backdrop-filter: blur(10px);">
                <h2 style="font-size: var(--text-lg); font-weight:600; color: var(--color-text-primary); margin:0 0 1rem; text-transform: uppercase; letter-spacing:0.05em;">Eliminar cuenta</h2>
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>
