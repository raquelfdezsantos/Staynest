@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; max-width: 600px;">
        <h1 style="font-family: var(--font-serif); font-size: var(--text-4xl); color: var(--color-accent); margin-bottom: var(--spacing-md);">404</h1>
        <h2 style="font-size: var(--text-2xl); color: var(--color-text-primary); margin-bottom: var(--spacing-lg); font-weight: 500;">Página no encontrada</h2>
        <p style="color: var(--color-text-secondary); margin-bottom: var(--spacing-xl); font-size: var(--text-base);">La página que intentas ver no existe o ha sido movida.</p>
        <a href="{{ route('home') }}" class="btn-action btn-action-primary sn-sentence" style="height: 36px; min-height: 36px; display: inline-flex; align-items: center; padding: 0 var(--spacing-lg);">Ir al inicio</a>
    </div>
</div>
@endsection
