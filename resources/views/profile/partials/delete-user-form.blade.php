<p style="margin-bottom: 1rem; font-size: var(--text-base); color: var(--color-text-secondary);">
    Una vez eliminada tu cuenta, todos tus datos se borrarán permanentemente. Antes de eliminarla, descarga cualquier información que desees conservar.
</p>

<button type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn-action btn-action-danger sn-sentence py-2 px-5"
        style="height:36px; display:inline-flex; align-items:center;">
    Eliminar cuenta
</button>
