@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page heading --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-text">System Notifications</h1>
            <p class="mt-1 text-sm text-muted">
                Broadcast messages shown to all authenticated users as dismissible banners.
            </p>
        </div>
        <a href="{{ route('admin.notifications.create') }}"
           class="inline-flex items-center gap-2 rounded-md bg-accent px-4 py-2 text-sm font-medium
                  text-white shadow-sm hover:bg-accent/90 focus:outline-none focus:ring-2
                  focus:ring-accent focus:ring-offset-2 transition-colors duration-150
                  dark:bg-purple-700 dark:hover:bg-purple-600 whitespace-nowrap shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Notification
        </a>
    </div>

    {{-- Notification table --}}
    {{--
        No overflow-x-auto here: that CSS property clips absolutely-positioned
        descendants (the action dropdowns) because it creates a new scroll
        container. We handle small-screen layout by hiding columns with
        responsive classes instead, so horizontal scrolling isn't needed.
    --}}
    <div>

        <p class="mb-3 text-xs text-muted" aria-live="polite">
            {{ $notifications->total() }} total {{ Str::plural('notification', $notifications->total()) }}.
        </p>

        <table class="w-full text-left text-sm border-separate border-spacing-y-2"
               aria-label="System notifications">
            <thead>
                <tr class="text-muted text-xs uppercase tracking-wide">
                    <th class="px-3 py-2" scope="col">Title &amp; Message</th>
                    <th class="px-3 py-2 w-px whitespace-nowrap" scope="col">Type</th>
                    <th class="px-3 py-2 w-px whitespace-nowrap" scope="col">Status</th>
                    <th class="px-3 py-2 w-32 hidden md:table-cell" scope="col">Expires</th>
                    <th class="px-3 py-2 w-20 hidden lg:table-cell" scope="col">Dismissed</th>
                    <th class="px-3 py-2 w-px whitespace-nowrap hidden lg:table-cell" scope="col">Created by</th>
                    <th class="px-3 py-2 w-px whitespace-nowrap" scope="col">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr class="bg-surface shadow-sm rounded-md align-top
                               {{ !$notification->is_active ? 'opacity-60' : '' }}">

                        {{-- Title & preview --}}
                        <td class="px-3 py-3">
                            <p class="font-medium text-text text-sm">
                                {{ $notification->title }}
                            </p>
                            <p class="mt-0.5 text-xs text-muted line-clamp-2">
                                {{ $notification->message }}
                            </p>
                        </td>

                        {{-- Type badge --}}
                        <td class="px-3 py-3">
                            @php
                                $typeBadge = match ($notification->type) {
                                    'info'    => 'bg-blue-100   dark:bg-blue-900/40   text-blue-700   dark:text-blue-300',
                                    'warning' => 'bg-amber-100  dark:bg-amber-900/40  text-amber-700  dark:text-amber-300',
                                    'success' => 'bg-green-100  dark:bg-green-900/40  text-green-700  dark:text-green-300',
                                    'danger'  => 'bg-red-100    dark:bg-red-900/40    text-red-700    dark:text-red-300',
                                    default   => 'bg-gray-100   dark:bg-gray-700      text-gray-600   dark:text-gray-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5
                                         text-xs font-medium whitespace-nowrap capitalize {{ $typeBadge }}">
                                {{ $notification->type }}
                            </span>
                        </td>

                        {{-- Active / Inactive / Expired status --}}
                        <td class="px-3 py-3">
                            @if ($notification->isExpired())
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5
                                             text-xs font-medium whitespace-nowrap
                                             bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                    Expired
                                </span>
                            @elseif ($notification->is_active)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5
                                             text-xs font-medium whitespace-nowrap
                                             bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5
                                             text-xs font-medium whitespace-nowrap
                                             bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Expiry date --}}
                        <td class="px-3 py-3 text-xs text-muted hidden md:table-cell">
                            @if ($notification->expires_at)
                                <span x-data
                                      x-init="
                                          $el.textContent = new Intl.DateTimeFormat(undefined, {
                                              month: 'short', day: 'numeric', year: 'numeric'
                                          }).format(new Date('{{ $notification->expires_at->toIso8601String() }}'))
                                      ">
                                    {{-- populated by Alpine --}}
                                </span>
                                @if ($notification->isExpired())
                                    <span class="text-red-500 dark:text-red-400">(expired)</span>
                                @endif
                            @else
                                <span class="italic">Never</span>
                            @endif
                        </td>

                        {{-- Dismissal count --}}
                        <td class="px-3 py-3 text-xs text-muted hidden lg:table-cell">
                            {{ $notification->dismissals_count }}
                            {{ Str::plural('user', $notification->dismissals_count) }}
                        </td>

                        {{-- Created by --}}
                        <td class="px-3 py-3 hidden lg:table-cell">
                            @if ($notification->createdBy)
                                <span class="text-text text-xs font-medium">
                                    {{ $notification->createdBy->name }}
                                </span>
                            @else
                                <span class="text-muted text-xs italic">Deleted user</span>
                            @endif
                        </td>

                        {{-- Actions dropdown --}}
                        <td class="px-3 py-3">
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        type="button"
                                        class="p-1.5 rounded text-muted hover:text-text hover:bg-bg
                                               focus:outline-none focus:ring-2 focus:ring-accent
                                               transition-colors duration-150"
                                        :aria-expanded="open.toString()"
                                        aria-haspopup="true"
                                        aria-label="Actions for {{ $notification->title }}">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.outside="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-1 w-44 origin-top-right rounded-md
                                            bg-surface border border-border shadow-lg
                                            focus:outline-none"
                                     role="menu"
                                     aria-orientation="vertical"
                                     x-cloak>

                                    <a href="{{ route('admin.notifications.edit', $notification) }}"
                                       class="block px-4 py-2 text-sm text-text hover:bg-bg
                                              transition-colors duration-150"
                                       role="menuitem">
                                        Edit
                                    </a>

                                    {{-- Activate / Deactivate toggle --}}
                                    @if ($notification->is_active && !$notification->isExpired())
                                        <form method="POST"
                                              action="{{ route('admin.notifications.deactivate', $notification) }}"
                                              class="m-0">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-left px-4 py-2 text-sm text-text
                                                           hover:bg-bg transition-colors duration-150"
                                                    role="menuitem">
                                                Deactivate
                                            </button>
                                        </form>
                                    @elseif (!$notification->isExpired())
                                        <form method="POST"
                                              action="{{ route('admin.notifications.activate', $notification) }}"
                                              class="m-0">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-left px-4 py-2 text-sm text-text
                                                           hover:bg-bg transition-colors duration-150"
                                                    role="menuitem">
                                                Activate
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST"
                                          action="{{ route('admin.notifications.destroy', $notification) }}"
                                          class="m-0"
                                          onsubmit="return confirm('Delete this notification? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm
                                                       text-red-600 dark:text-red-400
                                                       hover:bg-bg transition-colors duration-150"
                                                role="menuitem">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-10 text-center text-sm text-muted">
                            No notifications yet.
                            <a href="{{ route('admin.notifications.create') }}"
                               class="text-accent hover:underline ml-1">
                                Create one now →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
