<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2 bg-[color:var(--color-accent)] text-white font-semibold text-sm hover:bg-[color:var(--color-accent-hover)] transition ease-in-out duration-150 sn-sentence']) }} style="border-radius: 2px; white-space: nowrap;">
    {{ $slot }}
</button>
