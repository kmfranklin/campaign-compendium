@php
    use App\Models\Role;

    $roleLabels = [
        Role::DM => 'DM',
        Role::PLAYER => 'Player',
    ];
@endphp

<div class="mb-8">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Members</h2>

    {{-- Add Member Form (DM only) --}}
    @error('email')
        <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
    @enderror

    @if(session('success'))
        <p class="text-green-600 text-sm mb-4">{{ session('success') }}</p>
    @endif

    @can('addMember', $campaign)
        <form action="{{ route('campaigns.invites.store', $campaign) }}" method="POST" class="mb-6 flex gap-2">
            @csrf
            <input
                type="email"
                name="email"
                placeholder="User email"
                class="border border-gray-300 rounded px-3 py-2 w-64"
                value="{{ old('email') }}"
            >
            <button
                type="submit"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded"
            >
                Send Invite
            </button>
        </form>
    @endcan

    {{-- Member List --}}
    @if($campaign->members->count())
        <ul class="space-y-2">
            @foreach($campaign->members as $member)
                <li class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded border border-gray-200">
                    <div>
                        <span class="text-gray-900 font-medium">{{ $member->name }}</span>
                        <span class="text-xs text-gray-500 ml-2">
                            {{ $roleLabels[$member->pivot->role_id] ?? 'Unknown' }}
                        </span>
                    </div>

                    {{-- Remove button (DM + Co‑DM only, but never for the DM) --}}
                    @can('removeMember', $campaign)
                        @if($member->pivot->role_id !== Role::DM)
                            <form action="{{ route('campaigns.members.remove', $campaign) }}" method="POST"
                                  onsubmit="return confirm('Remove this member?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                <button
                                    type="submit"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
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

    @if($campaign->pendingInvites->count())
    <div class="mt-8">
        <h3 class="text-md font-semibold text-gray-700 mb-3">Pending Invites</h3>

        <ul class="space-y-2">
            @foreach ($campaign->pendingInvites as $invite)
                <li class="flex items-center justify-between bg-yellow-50 px-3 py-2 rounded border border-yellow-200">
                    <div>
                        <span class="text-gray-900 font-medium">{{ $invite->email }}</span>
                        @if($invite->invitee)
                            <span class="text-xs text-gray-500 ml-2">(User: {{ $invite->invitee->name }})</span>
                        @endif
                    </div>

                    <span class="text-xs text-gray-600">Pending</span>
                </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
