@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{!! __('Pagination Navigation') !!}" class="flex justify-between gap-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-surface border border-border cursor-default leading-5 rounded-md opacity-60">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-text bg-surface border border-border leading-5 rounded-md hover:bg-bg-elevated focus:outline-none focus:ring-2 focus:ring-accent transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-text bg-surface border border-border leading-5 rounded-md hover:bg-bg-elevated focus:outline-none focus:ring-2 focus:ring-accent transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-surface border border-border cursor-default leading-5 rounded-md opacity-60">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
