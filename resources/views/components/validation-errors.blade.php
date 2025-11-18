@props(['errorsBag' => $errors])

@if ($errorsBag->any())
    <x-alert type="error" {{ $attributes }}>
        <strong>Revisa lo siguiente:</strong>
        <ul style="margin-top: 0.5rem; padding-left: 1.25rem; list-style: disc;">
            @foreach ($errorsBag->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
