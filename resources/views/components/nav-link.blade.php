@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 border-b-2 h-24 border-accent text-sm font-semibold leading-5 text-text focus:outline-none focus:border-accent transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 border-b-2 h-24 border-transparent text-sm font-medium leading-5 text-muted hover:text-accent hover:border-accent/40 focus:outline-none focus:text-accent focus:border-accent/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
