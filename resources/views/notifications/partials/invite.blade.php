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
    <tr class="hover:bg-bg">
        <td class="px-6 py-4 text-sm text-text flex items-start gap-3">

            {{-- Unread dot --}}
            @if ($unread)
                <span class="inline-block w-2 h-2 mt-1 bg-accent rounded-full"></span>
            @endif

            <div>
                <span class="font-medium text-text">
                    {{ $notification->data['inviter_name'] }}
                </span>
                invited you to join
                <span class="font-medium text-accent">
                    {{ $notification->data['campaign_name'] }}
                </span>

                <div class="text-sm text-muted">
                    Received {{ $notification->created_at->diffForHumans() }}
                </div>

                @if ($timestamp)
                    <div class="text-sm text-muted">
                        You {{ strtolower($status) }} this invitation {{ $timestamp->diffForHumans() }}
                    </div>
                @endif
            </div>
        </td>

        <td class="px-6 py-4 text-sm whitespace-nowrap text-right">

            @if ($status === \App\Models\CampaignInvite::STATUS_PENDING)
                <div class="inline-flex gap-4">

                    {{-- Accept --}}
                    <form action="{{ route('invites.accept', $invite->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="text-accent hover:text-accent-hover font-medium">
                            Accept
                        </button>
                    </form>

                    {{-- Decline --}}
                    <form action="{{ route('invites.decline', $invite->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="text-muted hover:text-text font-medium">
                            Decline
                        </button>
                    </form>

                </div>
            @else
                {{-- Status pill --}}
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                    {{ $status === 'accepted'
                        ? 'bg-green-500/10 text-green-400'
                        : 'bg-red-500/10 text-red-400' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif

        </td>
    </tr>

@else
{{-- Mobile Layout --}}
    <div class="bg-surface border border-border shadow p-4 rounded-lg relative">

        {{-- Unread dot --}}
        @if ($unread)
            <span class="absolute top-3 left-3 w-2 h-2 bg-accent rounded-full"></span>
        @endif

        <p class="text-text pl-4">
            <span class="font-semibold">{{ $notification->data['inviter_name'] }}</span>
            invited you to join
            <span class="font-semibold text-accent">{{ $notification->data['campaign_name'] }}</span>
        </p>

        <div class="text-sm text-muted">
            Received {{ $notification->created_at->diffForHumans() }}
        </div>

        @if ($timestamp)
            <div class="text-sm text-muted">
                You {{ strtolower($status) }} this invitation {{ $timestamp->diffForHumans() }}
            </div>
        @endif

        <div class="mt-4 flex gap-3 justify-end">

            @if ($status === \App\Models\CampaignInvite::STATUS_PENDING)

                {{-- Accept --}}
                <form method="POST" action="{{ route('invites.accept', $invite->id) }}">
                    @csrf
                    <button class="px-4 py-2 bg-accent text-on-accent rounded hover:bg-accent-hover text-sm font-medium">
                        Accept
                    </button>
                </form>

                {{-- Decline --}}
                <form method="POST" action="{{ route('invites.decline', $invite->id) }}">
                    @csrf
                    <button class="px-4 py-2 bg-bg text-text border border-border rounded hover:bg-hover text-sm font-medium">
                        Decline
                    </button>
                </form>

            @else
                {{-- Status pill --}}
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ $status === 'accepted'
                        ? 'bg-green-500/10 text-green-400'
                        : 'bg-red-500/10 text-red-400' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif

        </div>
    </div>
@endif
