@props([
    'compact' => false,
])

@php
    $wrapperClasses = $compact
        ? 'inline-flex items-center'
        : 'inline-flex items-center';

    $campaignClasses = $compact
        ? 'text-[1.1rem] leading-none tracking-[0.01em]'
        : 'text-[1.32rem] leading-none tracking-[0.08em]';

    $compendiumClasses = $compact
        ? 'text-[1.1rem] leading-none tracking-[0.005em]'
        : 'text-[1.34rem] leading-none tracking-[0.01em]';

    $textBlockClasses = $compact
        ? 'flex min-w-0 flex-col items-start text-left font-semibold text-accent'
        : 'flex min-w-0 flex-col items-start text-left font-semibold text-accent';
@endphp

<span {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <span class="{{ $textBlockClasses }}">
        <span class="{{ $campaignClasses }}">Campaign</span>
        <span class="{{ $compendiumClasses }}">Compendium</span>
    </span>
</span>
