{{--
    System Notification Banners.

    Included in both layouts/app.blade.php and layouts/admin.blade.php,
    immediately after the <nav> element.

    Only shown for authenticated users. Queries for notifications that are:
      - is_active = true
      - expires_at is null OR expires_at > now()
      - NOT already dismissed by the current user (whereDoesntHave)

    Each banner uses Alpine x-data to track its own `dismissed` state.
    When the dismiss button is clicked:
      1. `dismissed` is set to true immediately — the banner fades out via
         x-transition so the user gets instant feedback.
      2. A fetch() POST is sent to the dismiss endpoint in the background
         so the dismissal is recorded in the database for future page loads.

    The fetch() uses the CSRF token from the <meta name="csrf-token"> tag
    that is present in both layouts.

    If the user has JavaScript disabled, a <noscript> fallback form is
    rendered inside each banner so dismissal still works via a plain POST.
--}}

@auth
    @php
        $systemNotifications = \App\Models\SystemNotification::visibleForUser(auth()->user())
            ->orderBy('created_at', 'asc')
            ->get();
    @endphp

    @if ($systemNotifications->isNotEmpty())
        <div class="space-y-0" role="region" aria-label="System announcements">
            @foreach ($systemNotifications as $sysNotification)
                @php
                    // Banner colour palette keyed by notification type.
                    $bannerStyles = match ($sysNotification->type) {
                        'info'    => [
                            'wrapper' => 'bg-blue-50   dark:bg-blue-950/50  border-blue-200  dark:border-blue-800',
                            'icon'    => 'text-blue-500 dark:text-blue-400',
                            'title'   => 'text-blue-800 dark:text-blue-200',
                            'message' => 'text-blue-700 dark:text-blue-300',
                            'button'  => 'text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-200',
                        ],
                        'warning' => [
                            'wrapper' => 'bg-amber-50  dark:bg-amber-950/50 border-amber-200 dark:border-amber-800',
                            'icon'    => 'text-amber-500 dark:text-amber-400',
                            'title'   => 'text-amber-800 dark:text-amber-200',
                            'message' => 'text-amber-700 dark:text-amber-300',
                            'button'  => 'text-amber-500 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-200',
                        ],
                        'success' => [
                            'wrapper' => 'bg-green-50  dark:bg-green-950/50 border-green-200 dark:border-green-800',
                            'icon'    => 'text-green-500 dark:text-green-400',
                            'title'   => 'text-green-800 dark:text-green-200',
                            'message' => 'text-green-700 dark:text-green-300',
                            'button'  => 'text-green-500 dark:text-green-400 hover:text-green-700 dark:hover:text-green-200',
                        ],
                        'danger'  => [
                            'wrapper' => 'bg-red-50    dark:bg-red-950/50   border-red-200   dark:border-red-800',
                            'icon'    => 'text-red-500  dark:text-red-400',
                            'title'   => 'text-red-800  dark:text-red-200',
                            'message' => 'text-red-700  dark:text-red-300',
                            'button'  => 'text-red-500  dark:text-red-400  hover:text-red-700  dark:hover:text-red-200',
                        ],
                        default   => [
                            'wrapper' => 'bg-gray-50   dark:bg-gray-800     border-gray-200  dark:border-gray-700',
                            'icon'    => 'text-gray-500 dark:text-gray-400',
                            'title'   => 'text-gray-800 dark:text-gray-200',
                            'message' => 'text-gray-700 dark:text-gray-300',
                            'button'  => 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200',
                        ],
                    };
                @endphp

                {{--
                    Each banner is its own Alpine component so dismissing one
                    does not affect the others. `dismissed` starts false;
                    clicking dismiss sets it true, triggering the leave transition.
                --}}
                <div x-data="{ dismissed: false }"
                     x-show="!dismissed"
                     x-transition:leave="transition-all duration-300 ease-in"
                     x-transition:leave-start="opacity-100 max-h-40"
                     x-transition:leave-end="opacity-0 max-h-0 overflow-hidden"
                     class="border-b {{ $bannerStyles['wrapper'] }}"
                     role="alert"
                     aria-live="polite">

                    <div class="w-full px-4 sm:px-6 lg:px-8 py-3 flex items-start gap-3">

                        {{-- Type icon --}}
                        <div class="shrink-0 mt-0.5" aria-hidden="true">
                            @if ($sysNotification->type === 'info')
                                <svg class="w-5 h-5 {{ $bannerStyles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($sysNotification->type === 'warning')
                                <svg class="w-5 h-5 {{ $bannerStyles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @elseif ($sysNotification->type === 'success')
                                <svg class="w-5 h-5 {{ $bannerStyles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($sysNotification->type === 'danger')
                                <svg class="w-5 h-5 {{ $bannerStyles['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold {{ $bannerStyles['title'] }}">
                                {{ $sysNotification->title }}
                            </p>
                            <p class="text-sm {{ $bannerStyles['message'] }} mt-0.5">
                                {{ $sysNotification->message }}
                            </p>
                        </div>

                        {{-- Dismiss button --}}
                        <button type="button"
                                @click="
                                    dismissed = true;
                                    fetch('{{ route('system-notifications.dismiss', $sysNotification) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        },
                                    });
                                "
                                class="shrink-0 rounded p-1 {{ $bannerStyles['button'] }}
                                       focus:outline-none focus:ring-2 focus:ring-current
                                       transition-colors duration-150"
                                aria-label="Dismiss: {{ $sysNotification->title }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>

                    {{-- No-JS fallback: plain form POST if JavaScript is disabled. --}}
                    <noscript>
                        <form method="POST"
                              action="{{ route('system-notifications.dismiss', $sysNotification) }}"
                              class="px-4 sm:px-6 lg:px-8 pb-3">
                            @csrf
                            <button type="submit"
                                    class="text-xs underline {{ $bannerStyles['button'] }}">
                                Dismiss this notification
                            </button>
                        </form>
                    </noscript>

                </div>

            @endforeach
        </div>
    @endif
@endauth
