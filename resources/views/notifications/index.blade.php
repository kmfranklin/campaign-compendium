@extends('layouts.app')

@section('content')
<div class="w-full">

    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Notifications</h1>

        @if (in_array($active, ['unread', 'all']))
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="text-sm text-muted hover:text-text">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- Desktop List --}}
    <div class="hidden sm:block">
        <div class="w-full max-w-7xl mx-auto bg-surface border border-border shadow-sm sm:rounded-lg">

            {{-- Tabs --}}
            <div class="border-b border-border mb-6">
                <nav class="flex gap-6 text-sm">

                    <a href="?filter=unread"
                       class="{{ $active === 'unread'
                                ? 'text-accent font-semibold'
                                : 'text-muted hover:text-text' }}">
                        Unread
                    </a>

                    <a href="?filter=read"
                       class="{{ $active === 'read'
                                ? 'text-accent font-semibold'
                                : 'text-muted hover:text-text' }}">
                        Read
                    </a>

                    <a href="?filter=all"
                       class="{{ $active === 'all'
                                ? 'text-accent font-semibold'
                                : 'text-muted hover:text-text' }}">
                        All
                    </a>

                </nav>
            </div>

            <table class="min-w-full table-auto">
                <thead class="bg-bg border-b border-border">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-muted uppercase tracking-wider">
                            Message
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                @forelse($notifications as $notification)
                    <x-notification-item :notification="$notification" layout="desktop" />
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-center text-muted">
                            No notifications found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="sm:hidden space-y-4 mt-6">
        @forelse($notifications as $notification)
            <x-notification-item :notification="$notification" layout="mobile" />
        @empty
            <p class="text-center text-muted">No notifications found.</p>
        @endforelse
    </div>

</div>
@endsection
