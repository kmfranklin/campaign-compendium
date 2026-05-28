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
    <tr class="ui-table-row">
        <td class="ui-table-cell-strong">
            <div class="flex items-start gap-3">

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
            </div>
        </td>

        <td class="ui-table-cell whitespace-nowrap text-right">

            @if ($status === \App\Models\CampaignInvite::STATUS_PENDING)
                <div class="ui-table-action-row justify-end">

                    {{-- Accept --}}
                    <form action="{{ route('invites.accept', $invite->token) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="ui-table-action-primary">
                            Accept
                        </button>
                    </form>

                    {{-- Decline --}}
                    <form action="{{ route('invites.decline', $invite->token) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="ui-table-action-danger">
                            Decline
                        </button>
                    </form>

                </div>
            @else
                {{-- Status pill --}}
                <span class="{{ $status === 'accepted' ? 'ui-badge ui-badge-success' : 'ui-badge ui-badge-danger' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif

        </td>
    </tr>

@else
{{-- Mobile Layout --}}
    <div class="ui-card p-4 relative">

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
                <form method="POST" action="{{ route('invites.accept', $invite->token) }}">
                    @csrf
                    <button class="btn btn-primary btn-sm">
                        Accept
                    </button>
                </form>

                {{-- Decline --}}
                <form method="POST" action="{{ route('invites.decline', $invite->token) }}">
                    @csrf
                    <button class="btn btn-secondary btn-sm">
                        Decline
                    </button>
                </form>

            @else
                {{-- Status pill --}}
                <span class="{{ $status === 'accepted' ? 'ui-badge ui-badge-success' : 'ui-badge ui-badge-danger' }}">
                    {{ ucfirst($status) }}
                </span>
            @endif

        </div>
    </div>
@endif
