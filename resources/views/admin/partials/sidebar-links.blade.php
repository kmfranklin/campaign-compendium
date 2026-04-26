@php
    /**
     * Active-state detection. Using wildcards so any page within a section
     * (list, edit, show, etc.) keeps the correct sidebar item highlighted.
     */
    $isDashboard     = request()->routeIs('admin.dashboard');
    $isUsers         = request()->routeIs('admin.users.*');
    $isNotifications = request()->routeIs('admin.notifications.*');
    $isActivity      = request()->routeIs('admin.activity.*');

    /**
     * Shared base class applied to every link and disabled-span.
     *
     * The border-l-2 + pl-[14px] pattern keeps ALL items at the same horizontal
     * indent: a 2 px left border visually occupies 2 px, so padding-left is set
     * to 14 px (= 16 px − 2 px) to make the text align with items that have a
     * transparent border. Without this, text would shift 2 px horizontally when
     * an item becomes active — a small but noticeable jitter.
     *
     * justify-start is the default; collapsed state (desktop only) overrides this
     * to justify-center via Alpine.
     */
    $linkBase = 'group flex items-center gap-3 py-2.5 pr-4 text-sm font-medium rounded-md
                 transition-colors duration-150 border-l-2 pl-[14px]';

    /**
     * Active: purple left border + faint purple background.
     * Light: accent (#6d28d9) is dark enough on white (7.4:1 ✓).
     * Dark: purple-300 (#c4b5fd) on #1f2937 = 8.2:1 ✓ (WCAG AAA).
     *       bg-purple-950/60 is a very dark translucent purple that gives
     *       a visible but subtle tinted background without being distracting.
     */
    $linkActive = 'border-accent dark:border-purple-400
                   bg-accent/10 dark:bg-purple-950/60
                   text-accent dark:text-purple-300';

    /**
     * Inactive: transparent border keeps spacing, muted text with hover states.
     */
    $linkInactive = 'border-transparent text-muted
                     hover:text-text hover:bg-bg';

    /**
     * Disabled (route not built yet): same spacing, dimmed with opacity.
     */
    $linkDisabled = 'border-transparent text-muted opacity-40 cursor-not-allowed';
@endphp

<ul role="list" class="space-y-1">

    {{-- Dashboard --}}
    <li>
        <a href="{{ route('admin.dashboard') }}"
           class="{{ $linkBase }} {{ $isDashboard ? $linkActive : $linkInactive }}"
           :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
           :title="expanded ? null : 'Dashboard'"
           @if($isDashboard) aria-current="page" @endif>
            <svg class="w-5 h-5 flex-shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="expanded"
                  x-transition:enter="transition-opacity duration-150 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="whitespace-nowrap">
                Dashboard
            </span>
        </a>
    </li>

    {{-- User Management --}}
    <li>
        <a href="{{ route('admin.users.index') }}"
           class="{{ $linkBase }} {{ $isUsers ? $linkActive : $linkInactive }}"
           :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
           :title="expanded ? null : 'User Management'"
           @if($isUsers) aria-current="page" @endif>
            <svg class="w-5 h-5 flex-shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span x-show="expanded"
                  x-transition:enter="transition-opacity duration-150 delay-100"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="transition-opacity duration-100"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="whitespace-nowrap">
                User Management
            </span>
        </a>
    </li>

    {{-- System Notifications --}}
    <li>
        @if (Route::has('admin.notifications.index'))
            <a href="{{ route('admin.notifications.index') }}"
               class="{{ $linkBase }} {{ $isNotifications ? $linkActive : $linkInactive }}"
               :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
               :title="expanded ? null : 'System Notifications'"
               @if($isNotifications) aria-current="page" @endif>
                <svg class="w-5 h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="expanded"
                      x-transition:enter="transition-opacity duration-150 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">
                    System Notifications
                </span>
            </a>
        @else
            <span class="{{ $linkBase }} {{ $linkDisabled }}"
                  :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
                  :title="expanded ? null : 'System Notifications (coming soon)'"
                  aria-disabled="true">
                <svg class="w-5 h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="expanded"
                      x-transition:leave="transition-opacity duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap flex items-center gap-2">
                    System Notifications
                    <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                 bg-gray-200 dark:bg-gray-700
                                 text-gray-500 dark:text-gray-400"
                          aria-label="(coming soon)">
                        Soon
                    </span>
                </span>
            </span>
        @endif
    </li>

    {{-- Activity Log --}}
    <li>
        @if (Route::has('admin.activity.index'))
            <a href="{{ route('admin.activity.index') }}"
               class="{{ $linkBase }} {{ $isActivity ? $linkActive : $linkInactive }}"
               :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
               :title="expanded ? null : 'Activity Log'"
               @if($isActivity) aria-current="page" @endif>
                <svg class="w-5 h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span x-show="expanded"
                      x-transition:enter="transition-opacity duration-150 delay-100"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">
                    Activity Log
                </span>
            </a>
        @else
            <span class="{{ $linkBase }} {{ $linkDisabled }}"
                  :class="expanded ? 'justify-start' : 'justify-center px-0 pl-0 border-l-0'"
                  :title="expanded ? null : 'Activity Log (coming soon)'"
                  aria-disabled="true">
                <svg class="w-5 h-5 flex-shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span x-show="expanded"
                      x-transition:leave="transition-opacity duration-100"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap flex items-center gap-2">
                    Activity Log
                    <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                 bg-gray-200 dark:bg-gray-700
                                 text-gray-500 dark:text-gray-400"
                          aria-label="(coming soon)">
                        Soon
                    </span>
                </span>
            </span>
        @endif
    </li>

</ul>
