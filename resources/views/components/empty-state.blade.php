@props([
    'icon'        => '📄',
    'title'       => 'Nothing here yet',
    'message'     => null,
    'action'      => null,
    'actionLabel' => null,
])

<div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-border bg-surface py-10 px-6 text-center">
    <div class="text-4xl mb-3">{{ $icon }}</div>

    <h3 class="text-lg font-medium text-text">{{ $title }}</h3>

    @if($message)
        <p class="mt-1 text-sm text-muted">{{ $message }}</p>
    @endif

    @if($action && $actionLabel)
        <div class="mt-4">
            <a href="{{ $action }}"
               class="inline-flex items-center rounded-md bg-accent px-4 py-2 text-sm font-medium text-on-accent shadow-sm hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
