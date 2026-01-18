@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Notifications</h1>
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
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-6 py-4 text-sm text-gray-800">
                                <span class="font-medium text-gray-900">{{ $notification->data['inviter_name'] }}</span>
                                invited you to join
                                <span class="font-medium text-purple-700">{{ $notification->data['campaign_name'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap text-right">
                                <div class="inline-flex gap-4">
                                    <form action="{{ route('invites.accept', $notification->notifiable_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-purple-700 hover:text-purple-900 font-medium focus:outline-none focus:ring-2 focus:ring-purple-300">
                                            Accept
                                        </button>
                                    </form>
                                    <form action="{{ route('invites.decline', $notification->notifiable_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-gray-300">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
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
    <div class="sm:hidden space-y-4">
        @forelse($notifications as $notification)
            <div class="bg-white border border-gray-200 shadow p-4 rounded-lg">
                <p class="text-gray-800">
                    <span class="font-semibold">{{ $notification->data['inviter_name'] }}</span>
                    invited you to join
                    <span class="font-semibold text-purple-700">{{ $notification->data['campaign_name'] }}</span>
                </p>
                <div class="mt-4 flex gap-3 justify-end">
                    <form method="POST" action="{{ route('invites.accept', $notification->notifiable_id) }}">
                        @csrf
                        <button class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-900 text-sm font-medium">
                            Accept
                        </button>
                    </form>
                    <form method="POST" action="{{ route('invites.decline', $notification->notifiable_id) }}">
                        @csrf
                        <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm font-medium">
                            Decline
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-700">No notifications found.</p>
        @endforelse
    </div>
</div>
@endsection
