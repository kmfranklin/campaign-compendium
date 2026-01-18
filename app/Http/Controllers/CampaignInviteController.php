<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvite;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignInviteController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        $this->authorize('addMember', $campaign);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;

        // Check if an invite already exists
        $existing = $campaign->invites()
            ->where('email', $email)
            ->where('status', CampaignInvite::STATUS_PENDING)
            ->first();

        if ($existing) {
            return back()->withErrors([
                'email' => 'An invite has already been sent to this email.',
            ]);
        }

        // Check if the user already exists
        $invitee = User::where('email', $email)->first();

        // Create the invite
        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id'  => auth()->id(),
            'invitee_id'  => $invitee?->id,
            'email'       => $email,
            'token'       => Str::uuid(),
            'status'      => CampaignInvite::STATUS_PENDING,
        ]);

        // If the user exists, create a notification
        if ($invitee) {
            Notification::create([
                'user_id'         => $invitee->id,
                'type'            => 'campaign_invite',
                'notifiable_type' => CampaignInvite::class,
                'notifiable_id'   => $invite->id,
                'data'            => [
                    'campaign_name' => $campaign->name,
                    'inviter_name'  => auth()->user()->name,
                ],
            ]);
        }

        return back()->with('success', 'Invitation sent.');
    }

    public function accept(CampaignInvite $invite)
    {
        $user = auth()->user();

        // Ensure the logged-in user is the invitee
        if (! $user || ($user->id !== $invite->invitee_id && $user->email !== $invite->email)) {
            abort(403);
        }

        // Ensure the invite is still pending
        if ($invite->status !== CampaignInvite::STATUS_PENDING) {
            return back()->withErrors(['invite' => 'This invite is no longer active.']);
        }

        // Add the user to the campaign
        $invite->campaign->members()->attach($user->id, [
            'role_id' => \App\Models\Role::PLAYER,
        ]);

        // Update invite status
        $invite->update([
            'status'      => CampaignInvite::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        // Mark related notification as read
        Notification::where('notifiable_type', CampaignInvite::class)
            ->where('notifiable_id', $invite->id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'You have joined the campaign.');
    }

    public function decline(CampaignInvite $invite)
    {
        $user = auth()->user();

        // Ensure the logged-in user is the invitee
        if (! $user || ($user->id !== $invite->invitee_id && $user->email !== $invite->email)) {
            abort(403);
        }

        // Ensure the invite is still pending
        if ($invite->status !== CampaignInvite::STATUS_PENDING) {
            return back()->withErrors(['invite' => 'This invite is no longer active.']);
        }

        // Update invite status
        $invite->update([
            'status'      => CampaignInvite::STATUS_DECLINED,
            'declined_at' => now(),
        ]);

        // Mark related notification as read
        Notification::where('notifiable_type', CampaignInvite::class)
            ->where('notifiable_id', $invite->id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'You declined the invitation.');
    }
}
