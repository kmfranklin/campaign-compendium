@extends('layouts.admin')

@section('content')
<div class="max-w-4xl">

    <div>
        <h1 class="text-2xl font-semibold text-text">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-muted">
            Welcome, {{ auth()->user()->name }}. Choose a tool below to get started.
        </p>
    </div>

    {{--
        Tool cards grid. Each card links to a section of the admin area.
        Cards for sections that haven't been built yet are rendered as non-interactive
        <div> elements styled with opacity-50 and cursor-not-allowed, and include a
        "Coming soon" badge. Route::has() guards the route() call so we don't throw
        an exception on unregistered route names.
    --}}
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- User Management --}}
        <a href="{{ route('admin.users.index') }}"
           class="group flex flex-col p-6 bg-surface border border-border rounded-lg shadow-sm
                  hover:border-accent hover:shadow transition-all duration-150">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-md bg-accent/10 text-accent"
                     aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-text">User Management</h2>
            </div>
            <p class="text-sm text-muted">
                View, edit, suspend, and manage all user accounts.
            </p>
        </a>

        {{-- System Notifications --}}
        @if (Route::has('admin.notifications.index'))
            <a href="{{ route('admin.notifications.index') }}"
               class="group flex flex-col p-6 bg-surface border border-border rounded-lg shadow-sm
                      hover:border-accent hover:shadow transition-all duration-150">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-md bg-accent/10 text-accent"
                         aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-text">System Notifications</h2>
                </div>
                <p class="text-sm text-muted">
                    Broadcast announcements to all users or specific individuals.
                </p>
            </a>
        @else
            <div class="flex flex-col p-6 bg-surface border border-border rounded-lg shadow-sm opacity-50 cursor-not-allowed"
                 aria-disabled="true"
                 title="Coming soon">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500"
                         aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-text">System Notifications</h2>
                        <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                     bg-gray-200 dark:bg-gray-700
                                     text-gray-500 dark:text-gray-400">
                            Coming soon
                        </span>
                    </div>
                </div>
                <p class="text-sm text-muted">
                    Broadcast announcements to all users or specific individuals.
                </p>
            </div>
        @endif

        {{-- Activity Log --}}
        @if (Route::has('admin.activity.index'))
            <a href="{{ route('admin.activity.index') }}"
               class="group flex flex-col p-6 bg-surface border border-border rounded-lg shadow-sm
                      hover:border-accent hover:shadow transition-all duration-150">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-md bg-accent/10 text-accent"
                         aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-text">Activity Log</h2>
                </div>
                <p class="text-sm text-muted">
                    Review a full history of admin actions and impersonation events.
                </p>
            </a>
        @else
            <div class="flex flex-col p-6 bg-surface border border-border rounded-lg shadow-sm opacity-50 cursor-not-allowed"
                 aria-disabled="true"
                 title="Coming soon">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500"
                         aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-text">Activity Log</h2>
                        <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                     bg-gray-200 dark:bg-gray-700
                                     text-gray-500 dark:text-gray-400">
                            Coming soon
                        </span>
                    </div>
                </div>
                <p class="text-sm text-muted">
                    Review a full history of admin actions and impersonation events.
                </p>
            </div>
        @endif

    </div>
</div>
@endsection
