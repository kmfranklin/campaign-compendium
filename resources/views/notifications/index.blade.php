@extends('layouts.app')

@section('content')
<div class="w-full">

    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Notifications</h1>

        @if (in_array($active, ['unread', 'all']))
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="link-action-subtle">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- Desktop List --}}
    <div class="hidden sm:block">
        <div class="ui-table-panel w-full max-w-7xl mx-auto sm:rounded-lg">

            {{-- Tabs --}}
            <nav class="ui-tab-nav" aria-label="Notification filters">
                <a href="?filter=unread"
                   class="ui-tab-btn {{ $active === 'unread' ? 'ui-tab-btn-active' : '' }}">
                    Unread
                </a>

                <a href="?filter=read"
                   class="ui-tab-btn {{ $active === 'read' ? 'ui-tab-btn-active' : '' }}">
                    Read
                </a>

                <a href="?filter=all"
                   class="ui-tab-btn {{ $active === 'all' ? 'ui-tab-btn-active' : '' }}">
                    All
                </a>
            </nav>

            <table class="ui-table">
                <thead class="ui-table-head">
                    <tr>
                        <th class="ui-table-header">
                            Message
                        </th>
                        <th class="ui-table-header text-right">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                @forelse($notifications as $notification)
                    <x-notification-item :notification="$notification" layout="desktop" />
                @empty
                    <tr>
                        <td colspan="2" class="ui-table-empty">
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
