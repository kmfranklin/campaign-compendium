@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 border-b-2 h-24 border-purple-800 text-sm font-medium leading-5 text-text focus:outline-none focus:border-purple-900 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 border-b-2 h-24 border-transparent text-sm font-medium leading-5 text-muted hover:text-text hover:border-border focus:outline-none focus:text-text focus:border-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
