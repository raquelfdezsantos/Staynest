<p style="margin-bottom: 1rem; font-size: var(--text-base); color: var(--color-text-secondary);">
    Una vez eliminada tu cuenta, todos tus datos se borrarán permanentemente. Antes de eliminarla, descarga cualquier información que desees conservar.
</p>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn-action btn-action-danger sn-sentence py-2 px-5">
        Eliminar cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable @click.away="$dispatch('close-modal', 'confirm-user-deletion')">
        <div @click.away="$dispatch('close-modal', 'confirm-user-deletion')" style="padding: 2rem; border-radius: var(--radius-base); border: 1px solid rgba(var(--color-border-rgb), 0.1); background: rgba(var(--color-bg-secondary-rgb), 0.9); backdrop-filter: blur(10px);">
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
                            style="background-color: var(--color-bg-secondary); color: var(--color-text-primary); border: 1px solid var(--color-accent); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-accent)'; this.style.backgroundColor = 'rgba(77, 141, 148, 0.10)'; this.style.border = 'none';"
                            onmouseout="this.style.backgroundColor = 'var(--color-bg-secondary)'; this.style.color = 'var(--color-text-primary)'; this.style.border = '1px solid var(--color-accent)';">
                        Cancelar
                    </button>

                    <button type="submit" class="btn-action btn-action-danger sn-sentence py-2 px-5"
                            style="background-color: var(--color-error); color: #fff; border: 1px solid var(--color-error); border-radius: var(--radius-base); transition: all 0.3s ease; font-size: 14px;"
                            onmouseover="this.style.color = 'var(--color-error)'; this.style.backgroundColor = 'rgba(204, 89, 86, 0.10)'; this.style.borderColor = 'transparent';"
                            onmouseout="this.style.backgroundColor = 'var(--color-error)'; this.style.color = '#fff'; this.style.borderColor = 'var(--color-error)';">
                        Eliminar cuenta
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
