<x-guest-layout>
    @php
        $isPending = $invite->status === \App\Models\CampaignInvite::STATUS_PENDING;
        $isExpired = $invite->status === \App\Models\CampaignInvite::STATUS_EXPIRED || $invite->isExpired();
        $user = auth()->user();
    @endphp

    <div class="space-y-6">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-muted">Campaign Invite</p>
            <h1 class="mt-2 text-3xl font-semibold text-text">{{ $invite->campaign->name }}</h1>
            <p class="mt-3 text-sm text-muted">
                <strong class="text-text">{{ $invite->inviter->name }}</strong> invited
                <strong class="text-text">{{ $invite->email }}</strong>
                to join this campaign on Campaign Compendium.
            </p>
        </div>

        @if ($invite->expires_at)
            <p class="text-sm text-muted">
                Expires {{ $invite->expires_at->toDayDateTimeString() }}
            </p>
        @endif

        @if ($errors->has('invite'))
            <div class="rounded-lg border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
                {{ $errors->first('invite') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-lg border border-accent/30 bg-accent/10 px-4 py-3 text-sm text-text">
                {{ session('success') }}
            </div>
        @endif

        @if ($isExpired)
            <div class="rounded-lg border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
                This invite has expired.
            </div>
        @elseif (! $isPending)
            <div class="rounded-lg border border-border bg-bg px-4 py-3 text-sm text-text">
                This invite has already been {{ $invite->status }}.
            </div>
        @elseif ($user && $matchingUser)
            <div class="rounded-lg border border-border bg-bg px-4 py-4">
                <p class="text-sm text-text">Signed in as <strong>{{ $user->email }}</strong>.</p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('invites.accept', $invite->token) }}">
                        @csrf
                        <button class="btn btn-primary">Join Campaign</button>
                    </form>

                    <form method="POST" action="{{ route('invites.decline', $invite->token) }}">
                        @csrf
                        <button class="btn btn-secondary">Decline Invite</button>
                    </form>
                </div>
            </div>
        @elseif ($user)
            <div class="rounded-lg border border-danger/30 bg-danger/10 px-4 py-3 text-sm text-danger">
                This invite was sent to <strong>{{ $invite->email }}</strong>, but you're signed in as
                <strong>{{ $user->email }}</strong>.
            </div>
        @else
            <div class="rounded-lg border border-border bg-bg px-4 py-4">
                <p class="text-sm text-text">
                    Create an account or sign in with <strong>{{ $invite->email }}</strong> to join this campaign.
                </p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('register', ['invite' => $invite->token]) }}" class="btn btn-primary">
                        Create Account
                    </a>
                    <a href="{{ route('login', ['invite' => $invite->token]) }}" class="btn btn-secondary">
                        Sign In
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-guest-layout>
