@props(['status'])

@if ($status)
    <x-alert type="success" {{ $attributes }}>
        {{ $status }}
    </x-alert>
@endif
