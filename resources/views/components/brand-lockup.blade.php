@props([
    'compact' => false,
])

@php
    $wrapperClasses = $compact
        ? 'inline-flex items-center'
        : 'inline-flex items-center';

    $campaignClasses = $compact
        ? 'block text-[1rem] leading-[0.9] tracking-[0.125em]'
        : 'block text-[1.28rem] leading-[0.9] tracking-[0.145em]';

    $compendiumClasses = $compact
        ? 'block text-[0.94rem] leading-[0.9] tracking-[0.005em]'
        : 'block text-[1.14rem] leading-[0.9] tracking-[0.01em]';

    $textBlockClasses = $compact
        ? 'flex w-[7.25rem] min-w-0 flex-col items-stretch text-left font-display font-semibold text-accent'
        : 'flex w-[10.45rem] min-w-0 flex-col items-stretch text-left font-display font-semibold text-accent';
@endphp

<span {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <span class="{{ $textBlockClasses }}">
        <span class="{{ $campaignClasses }}">Campaign</span>
        <span class="{{ $compendiumClasses }}">Compendium</span>
    </span>
</span>
