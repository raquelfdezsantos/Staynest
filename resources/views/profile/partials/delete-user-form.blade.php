@if(auth()->user()->role !== 'admin')
    <p style="margin-bottom: 1rem; font-size: var(--text-base); color: var(--color-text-secondary);">
        Una vez eliminada tu cuenta, todos tus datos se borrarán permanentemente. Antes de eliminarla, descarga cualquier información que desees conservar.
    </p>

    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn-action btn-action-danger sn-sentence py-2 px-5">
        Eliminar cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding: 2rem;">
            @csrf
            @method('delete')

            {{-- Campo oculto de username para accesibilidad --}}
            <input type="text" name="username" autocomplete="username" value="{{ auth()->user()->email }}" style="display: none;" aria-hidden="true">

            <h2 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary);">
                ¿Estás seguro de que deseas eliminar tu cuenta?
            </h2>

            <p style="margin-top: 0.5rem; font-size: var(--text-base); color: var(--color-text-secondary);">
                Una vez eliminada, todos tus datos se borrarán permanentemente. Por favor, introduce tu contraseña para confirmar.
            </p>

            <div style="margin-top: 1.5rem;">
                <x-input-label for="password" value="Contraseña" class="sr-only" />
                <x-text-input id="password"
                              name="password"
                              type="password"
                              class="mt-1 block w-3/4"
                              autocomplete="current-password"
                              placeholder="Contraseña" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button"
                        @click="$dispatch('close-modal', 'confirm-user-deletion')"
                        class="btn-action btn-action-secondary">
                    Cancelar
                </button>

                <button type="submit" class="btn-action btn-action-danger sn-sentence">
                    Eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
@endif
