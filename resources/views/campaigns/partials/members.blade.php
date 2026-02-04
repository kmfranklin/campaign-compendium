@php
    use App\Models\Role;

    $roleLabels = [
        Role::DM => 'DM',
        Role::PLAYER => 'Player',
    ];
@endphp

<div class="mb-8">
    <h2 class="text-lg font-semibold text-text mb-3">Members</h2>

    {{-- Add Member Form (DM only) --}}
    @error('email')
        <p class="text-danger text-sm mb-4">{{ $message }}</p>
    @enderror

    @if(session('success'))
        <p class="text-accent text-sm mb-4">{{ session('success') }}</p>
    @endif

    @can('addMember', $campaign)
        <form action="{{ route('campaigns.invites.store', $campaign) }}"
              method="POST"
              class="mb-6 flex gap-2">
            @csrf

            <input
                type="email"
                name="email"
                placeholder="User email"
                value="{{ old('email') }}"
                class="w-64 px-3 py-2 rounded border border-border bg-surface text-text
                       focus:border-accent focus:ring-accent"
            >

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-accent text-on-accent rounded shadow
                           hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-accent">
                Send Invite
            </button>
        </form>
    @endcan

    {{-- Member List --}}
    @if($campaign->members->count())
        <ul class="space-y-2">
            @foreach($campaign->members as $member)
                <li class="flex items-center justify-between bg-bg px-3 py-2 rounded border border-border">
                    <div>
                        <span class="text-text font-medium">{{ $member->name }}</span>
                        <span class="text-xs text-muted ml-2">
                            {{ $roleLabels[$member->pivot->role_id] ?? 'Unknown' }}
                        </span>
                    </div>

                    {{-- Remove button (DM + Co‑DM only, but never for the DM) --}}
                    @can('removeMember', $campaign)
                        @if($member->pivot->role_id !== Role::DM)
                            <form action="{{ route('campaigns.members.remove', $campaign) }}"
                                  method="POST"
                                  onsubmit="return confirm('Remove this member?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_id" value="{{ $member->id }}">

                                <button type="submit"
                                        class="inline-flex items-center text-danger hover:text-red-600 text-sm font-medium">
                                    Remove
                                </button>
                            </form>
                        @endif
                    @endcan
                </li>
            @endforeach
        </ul>
    @else
        <x-empty-state
            icon="👥"
            title="No members yet"
            message="Invite players to join your campaign."
        />
    @endif

    {{-- Pending Invites --}}
    @if($campaign->pendingInvites->count())
        <div class="mt-8">
            <h3 class="text-md font-semibold text-text mb-3">Pending Invites</h3>

            <ul class="space-y-2">
                @foreach ($campaign->pendingInvites as $invite)
                    <li class="flex items-center justify-between bg-surface px-3 py-2 rounded border border-border">
                        <div>
                            <span class="text-text font-medium">{{ $invite->email }}</span>

                            @if($invite->invitee)
                                <span class="text-xs text-muted ml-2">
                                    (User: {{ $invite->invitee->name }})
                                </span>
                            @endif
                        </div>

                        <span class="text-xs text-muted">Pending</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
