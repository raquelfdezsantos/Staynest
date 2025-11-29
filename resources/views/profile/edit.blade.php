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

        {{-- Modal de confirmación de eliminación --}}
        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <div style="padding: 2rem; border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.9); backdrop-filter: blur(10px);">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    {{-- Campo oculto de username para accesibilidad --}}
                    <input type="text" name="username" autocomplete="username" value="{{ auth()->user()->email }}" style="display: none;" aria-hidden="true">

                    <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                        ¿Estás seguro de que deseas eliminar tu cuenta?
                    </h2>

                    <p style="margin-bottom: 1.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                        Una vez eliminada, todos tus datos se borrarán permanentemente. Por favor, introduce tu contraseña para confirmar.
                    </p>

                    <div style="margin-bottom: 1.5rem;">
                        <x-input-label for="password" value="Contraseña" class="sr-only" />
                        <x-text-input id="password"
                                      name="password"
                                      type="password"
                                      class="block w-full"
                                      autocomplete="current-password"
                                      placeholder="Contraseña" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button"
                            @click="$dispatch('close-modal', 'confirm-user-deletion')"
                            class="btn-action btn-action-secondary sn-sentence py-2 px-5"
                            style="height:36px; display:inline-flex; align-items:center; background-color: var(--color-bg-secondary); color: var(--color-text-primary); border: 1px solid var(--color-accent); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-accent)'; this.style.backgroundColor = 'rgba(77, 141, 148, 0.10)'; this.style.border = 'none';"
                            onmouseout="this.style.backgroundColor = 'var(--color-bg-secondary)'; this.style.color = 'var(--color-text-primary)'; this.style.border = '1px solid var(--color-accent)';">
                            Cancelar
                        </button>

                        <button type="submit" class="btn-action btn-action-danger sn-sentence py-2 px-5"
                            style="height:36px; display:inline-flex; align-items:center; background-color: var(--color-error); color: #fff; border: 1px solid var(--color-error); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-error)'; this.style.backgroundColor = 'rgba(204, 89, 86, 0.10)'; this.style.borderColor = 'transparent';"
                            onmouseout="this.style.backgroundColor = 'var(--color-error)'; this.style.color = '#fff'; this.style.borderColor = 'var(--color-error)';">
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>
