@props([
    'type' => 'info', // success | error | warning | info
])

<div {{ $attributes->merge(['class' => 'alert alert-' . $type]) }}>
    {{ $slot }}
    
</div>
