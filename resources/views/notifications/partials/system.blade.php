@php
    /** @var \App\Models\Notification $notification */
    $unread  = $notification->isUnread();
    $data    = $notification->data ?? [];
    $title   = $data['title']   ?? 'System Notification';
    $message = $data['message'] ?? '';
    $type    = $data['type']    ?? 'info';

    // Colour tokens keyed by notification type — mirrors the banner palette.
    $styles = match ($type) {
        'warning' => [
            'icon'  => 'text-amber-500 dark:text-amber-400',
            'title' => 'text-amber-700 dark:text-amber-300',
        ],
        'success' => [
            'icon'  => 'text-green-500 dark:text-green-400',
            'title' => 'text-green-700 dark:text-green-300',
        ],
        'danger'  => [
            'icon'  => 'text-red-500 dark:text-red-400',
            'title' => 'text-red-700 dark:text-red-300',
        ],
        default   => [   // info
            'icon'  => 'text-blue-500 dark:text-blue-400',
            'title' => 'text-blue-700 dark:text-blue-300',
        ],
    };
@endphp

{{-- Desktop Layout --}}
@if ($layout === 'desktop')
    <tr class="hover:bg-bg">
        <td class="px-6 py-4 text-sm text-text">
            <div class="flex items-start gap-3">

                {{-- Unread dot --}}
                @if ($unread)
                    <span class="inline-block w-2 h-2 mt-1.5 flex-shrink-0 bg-accent rounded-full"
                          aria-label="Unread"></span>
                @else
                    <span class="inline-block w-2 h-2 mt-1.5 flex-shrink-0"></span>
                @endif

                {{-- Type icon --}}
                <div class="shrink-0 mt-0.5" aria-hidden="true">
                    @if ($type === 'warning')
                        <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    @elseif ($type === 'success')
                        <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif ($type === 'danger')
                        <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                {{-- Text content --}}
                <div>
                    <p class="font-medium {{ $styles['title'] }}">{{ $title }}</p>
                    <p class="text-text mt-0.5">{{ $message }}</p>
                    <p class="text-xs text-muted mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>

            </div>
        </td>

        {{-- No actions for system notifications --}}
        <td class="px-6 py-4 text-sm text-right text-muted">—</td>
    </tr>

@else
{{-- Mobile Layout --}}
    <div class="bg-surface border border-border shadow p-4 rounded-lg relative">

        <div class="flex items-start gap-3">

            {{-- Unread dot --}}
            @if ($unread)
                <span class="absolute top-3 left-3 w-2 h-2 bg-accent rounded-full"
                      aria-label="Unread"></span>
            @endif

            {{-- Type icon --}}
            <div class="shrink-0 mt-0.5 {{ $unread ? 'ml-4' : '' }}" aria-hidden="true">
                @if ($type === 'warning')
                    <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @elseif ($type === 'success')
                    <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif ($type === 'danger')
                    <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-4 h-4 {{ $styles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            {{-- Text content --}}
            <div class="flex-1">
                <p class="font-medium {{ $styles['title'] }}">{{ $title }}</p>
                <p class="text-sm text-text mt-0.5">{{ $message }}</p>
                <p class="text-xs text-muted mt-1">
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>

        </div>
    </div>
@endif
