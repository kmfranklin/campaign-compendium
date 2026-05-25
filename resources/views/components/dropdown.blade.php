@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1'
])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative z-50"
     x-data="{ id: $id('dropdown'), open: false }"
     @click.outside="open = false"
     @close.stop="open = false"
     @keydown.escape.window="open = false"
     @dropdown-open.window="if ($event.detail !== id) open = false">
    <div @click.stop="
            if (!open) {
                $dispatch('dropdown-open', id);
            }
            open = ! open;
        ">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 bg-surface"
         x-transition:enter-end="opacity-100 scale-100 bg-surface"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 bg-surface"
         x-transition:leave-end="opacity-0 scale-95 bg-surface"
         class="absolute top-full z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }} bg-surface"
         style="display: none;"
         @click.stop>

        <div class="rounded-md border border-border shadow bg-surface {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
