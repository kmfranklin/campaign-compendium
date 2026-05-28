@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="sm:flex sm:items-center sm:justify-between py-6">
        <h1 class="text-2xl font-semibold text-text">Campaigns</h1>

        <a href="{{ route('campaigns.create') }}"
           class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-hover text-on-accent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
            + New Campaign
        </a>
    </div>

    @if ($campaigns->isEmpty())
        @php
            $unreadInviteCount = auth()->user()->notifications()
                ->where('type', \App\Models\Notification::TYPE_INVITE)
                ->whereNull('read_at')
                ->count();
            $emptyStateMessage = $unreadInviteCount > 0
                ? "Create your own campaign or review your pending invites to join someone else's table."
                : 'Create your first campaign to start organizing quests, NPCs, and session notes.';
        @endphp

        <x-empty-state
            icon="🧭"
            title="No campaigns yet"
            :message="$emptyStateMessage"
            :action="route('campaigns.create')"
            actionLabel="Create Campaign"
        />

        @if ($unreadInviteCount > 0)
            <div class="mt-4 text-center">
                <a href="{{ route('notifications.index') }}" class="link-action">
                    Review {{ $unreadInviteCount }} pending {{ \Illuminate\Support\Str::plural('invite', $unreadInviteCount) }}
                </a>
            </div>
        @endif
    @else
        {{-- Results Table + Mobile Cards --}}
        @include('campaigns.partials.results', ['campaigns' => $campaigns])
    @endif
</div>
@endsection
