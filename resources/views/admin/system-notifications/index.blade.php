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
           class="btn btn-primary btn-sm whitespace-nowrap shrink-0">
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
    <div class="ui-table-shell">

        <p class="mb-3 text-xs text-muted" aria-live="polite">
            {{ $notifications->total() }} total {{ Str::plural('notification', $notifications->total()) }}.
        </p>

        <div class="ui-table-panel">
        <table class="ui-table" aria-label="System notifications">
            <thead class="ui-table-head">
                <tr>
                    <th class="ui-table-header-tight" scope="col">Title &amp; Message</th>
                    <th class="ui-table-header-tight w-px whitespace-nowrap" scope="col">Type</th>
                    <th class="ui-table-header-tight w-px whitespace-nowrap" scope="col">Status</th>
                    <th class="ui-table-header-tight hidden w-32 md:table-cell" scope="col">Expires</th>
                    <th class="ui-table-header-tight hidden w-20 lg:table-cell" scope="col">Dismissed</th>
                    <th class="ui-table-header-tight hidden w-px whitespace-nowrap lg:table-cell" scope="col">Created by</th>
                    <th class="ui-table-header-tight w-px whitespace-nowrap" scope="col">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr class="ui-table-row align-top {{ !$notification->is_active ? 'opacity-60' : '' }}">

                        {{-- Title & preview --}}
                        <td class="ui-table-cell-tight">
                            <p class="font-medium text-text text-sm">
                                {{ $notification->title }}
                            </p>
                            <p class="mt-0.5 text-xs text-muted line-clamp-2">
                                {{ $notification->message }}
                            </p>
                        </td>

                        {{-- Type badge --}}
                        <td class="ui-table-cell-tight">
                            @php
                                $typeBadge = match ($notification->type) {
                                    'info'    => 'ui-badge ui-badge-info',
                                    'warning' => 'ui-badge ui-badge-warning',
                                    'success' => 'ui-badge ui-badge-success',
                                    'danger'  => 'ui-badge ui-badge-danger',
                                    default   => 'ui-badge ui-badge-muted',
                                };
                            @endphp
                            <span class="capitalize {{ $typeBadge }}">
                                {{ $notification->type }}
                            </span>
                        </td>

                        {{-- Active / Inactive / Expired status --}}
                        <td class="ui-table-cell-tight">
                            @if ($notification->isExpired())
                                <span class="ui-badge ui-badge-muted">
                                    Expired
                                </span>
                            @elseif ($notification->is_active)
                                <span class="ui-badge ui-badge-success">
                                    Active
                                </span>
                            @else
                                <span class="ui-badge ui-badge-muted">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Expiry date --}}
                        <td class="ui-table-cell-tight hidden text-xs md:table-cell">
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
                                    <span class="text-danger">(expired)</span>
                                @endif
                            @else
                                <span class="italic">Never</span>
                            @endif
                        </td>

                        {{-- Dismissal count --}}
                        <td class="ui-table-cell-tight hidden text-xs lg:table-cell">
                            {{ $notification->dismissals_count }}
                            {{ Str::plural('user', $notification->dismissals_count) }}
                        </td>

                        {{-- Created by --}}
                        <td class="ui-table-cell-tight hidden lg:table-cell">
                            @if ($notification->createdBy)
                                <span class="text-text text-xs font-medium">
                                    {{ $notification->createdBy->name }}
                                </span>
                            @else
                                <span class="text-muted text-xs italic">Deleted user</span>
                            @endif
                        </td>

                        {{-- Actions dropdown --}}
                        <td class="ui-table-cell-tight">
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                        @keydown.escape.window="open = false"
                                        type="button"
                                        class="ui-table-menu-trigger h-8 w-8"
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
                                     class="ui-table-menu"
                                     role="menu"
                                     aria-orientation="vertical"
                                     x-cloak>

                                    <a href="{{ route('admin.notifications.edit', $notification) }}"
                                       class="ui-table-menu-item"
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
                                                    class="ui-table-menu-item"
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
                                                    class="ui-table-menu-item"
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
                                                class="ui-table-menu-item-danger"
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
                        <td colspan="7" class="ui-table-empty-tight py-10">
                            No notifications yet.
                            <a href="{{ route('admin.notifications.create') }}"
                               class="ml-1 text-accent hover:underline">
                                Create one now →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if ($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
