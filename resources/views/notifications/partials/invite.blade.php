@php
    /** @var \App\Models\Notification $notification */
    $invite = $notification->notifiable;
    $status = $invite->status;
    $unread = $notification->isUnread();

    $timestamp = match ($status) {
        \App\Models\CampaignInvite::STATUS_ACCEPTED => $invite->accepted_at,
        \App\Models\CampaignInvite::STATUS_DECLINED => $invite->declined_at,
        default => null,
    };
@endphp

{{-- Desktop Layout --}}
@if ($layout === 'desktop')
    <tr class="hover:bg-gray-100">
        <td class="px-6 py-4 text-sm text-gray-800 flex items-start gap-3">
            @if ($unread)
                <span class="inline-block w-2 h-2 mt-1 bg-purple-600 rounded-full"></span>
            @endif

            <div>
                <span class="font-medium text-gray-900">{{ $notification->data['inviter_name'] }}</span>
                invited you to join
                <span class="font-medium text-purple-700">{{ $notification->data['campaign_name'] }}</span>

                <div class="text-sm text-gray-500">
                    Received {{ $notification->created_at->diffForHumans() }}
                </div>

                @if ($timestamp)
                    <div class="text-sm text-gray-500">
                        You {{ strtolower($status) }} this invitation {{ $timestamp->diffForHumans() }}
                    </div>
                @endif
            </div>
        </td>

        <td class="px-6 py-4 text-sm whitespace-nowrap text-right">
            @if ($status === \App\Models\CampaignInvite::STATUS_PENDING)
                <div class="inline-flex gap-4">
                    <form action="{{ route('invites.accept', $invite->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-purple-700 hover:text-purple-900 font-medium">
                            Accept
                        </button>
                    </form>

                    <form action="{{ route('invites.decline', $invite->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800 font-medium">
                            Decline
                        </button>
                    </form>
                </div>
            @else
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                    {{ $status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif
        </td>
    </tr>
@else
{{-- Mobile cards --}}
    <div class="bg-white border border-gray-200 shadow p-4 rounded-lg relative">

        @if ($unread)
            <span class="absolute top-3 left-3 w-2 h-2 bg-purple-600 rounded-full"></span>
        @endif

        <p class="text-gray-800 pl-4">
            <span class="font-semibold">{{ $notification->data['inviter_name'] }}</span>
            invited you to join
            <span class="font-semibold text-purple-700">{{ $notification->data['campaign_name'] }}</span>
        </p>

        <div class="text-sm text-gray-500">
            Received {{ $notification->created_at->diffForHumans() }}
        </div>

        @if ($timestamp)
            <div class="text-sm text-gray-500">
                You {{ strtolower($status) }} this invitation {{ $timestamp->diffForHumans() }}
            </div>
        @endif

        <div class="mt-4 flex gap-3 justify-end">
            @if ($status === \App\Models\CampaignInvite::STATUS_PENDING)
                <form method="POST" action="{{ route('invites.accept', $invite->id) }}">
                    @csrf
                    <button class="px-4 py-2 bg-purple-800 text-white rounded hover:bg-purple-900 text-sm font-medium">
                        Accept
                    </button>
                </form>

                <form method="POST" action="{{ route('invites.decline', $invite->id) }}">
                    @csrf
                    <button class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm font-medium">
                        Decline
                    </button>
                </form>
            @else
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ $status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif
        </div>
    </div>
@endif
