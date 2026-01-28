@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>
        @if (in_array($active, ['unread', 'all']))
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="text-sm text-gray-600 hover:text-gray-900">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- Desktop List --}}
    <div class="hidden sm:block">
        <div class="w-full max-w-7xl mx-auto bg-white border border-gray-200 shadow-sm sm:rounded-lg">
            <div class="border-b mb-6">
                <nav class="flex gap-6 text-sm">
                    <a href="?filter=unread" class="{{ $active === 'unread' ? 'text-purple-700 font-semibold' : 'text-gray-600' }}">
                        Unread
                    </a>

                    <a href="?filter=read" class="{{ $active === 'read' ? 'text-purple-700 font-semibold' : 'text-gray-600' }}">
                        Read
                    </a>

                    <a href="?filter=all" class="{{ $active === 'all' ? 'text-purple-700 font-semibold' : 'text-gray-600' }}">
                        All
                    </a>
                </nav>
            </div>

            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($notifications as $notification)
                    <x-notification-item :notification="$notification" layout="desktop" />
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm text-center text-gray-700">
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
            <p class="text-center text-gray-700">No notifications found.</p>
        @endforelse
    </div>
</div>
@endsection
