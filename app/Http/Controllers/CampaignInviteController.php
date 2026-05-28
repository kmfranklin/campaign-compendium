<?php

namespace App\Http\Controllers;

use App\Mail\CampaignInviteMail;
use App\Models\Campaign;
use App\Models\CampaignInvite;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignInviteController extends Controller
{
    public function store(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorize('addMember', $campaign);

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = Str::lower($validated['email']);
        $invitee = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($invitee && $campaign->members()->where('user_id', $invitee->id)->exists()) {
            return back()->withErrors([
                'email' => 'That user is already a member of this campaign.',
            ]);
        }

        $existing = $campaign->invites()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->active()
            ->first();

        if ($existing) {
            return back()->withErrors([
                'email' => 'An active invite has already been sent to this email address.',
            ]);
        }

        $campaign->invites()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->pending()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get()
            ->each
            ->markExpired();

        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $request->user()->id,
            'invitee_id' => $invitee?->id,
            'email' => $email,
            'token' => (string) Str::uuid(),
            'status' => CampaignInvite::STATUS_PENDING,
            'expires_at' => now()->addDays(CampaignInvite::DEFAULT_EXPIRY_DAYS),
        ]);

        if ($invitee) {
            Notification::create([
                'user_id' => $invitee->id,
                'type' => Notification::TYPE_INVITE,
                'notifiable_type' => CampaignInvite::class,
                'notifiable_id' => $invite->id,
                'data' => [
                    'campaign_name' => $campaign->name,
                    'inviter_name' => $request->user()->name,
                ],
            ]);
        }

        Mail::to($email)->queue(new CampaignInviteMail($invite->load(['campaign', 'inviter'])));

        return back()->with('success', 'Invitation sent by email.');
    }

    public function show(Request $request, CampaignInvite $invite): View|RedirectResponse
    {
        $invite->loadMissing(['campaign.dm', 'inviter', 'invitee']);

        if ($invite->isPending() && $invite->isExpired()) {
            $invite->markExpired();
            $invite->refresh();
        }

        $user = $request->user();

        if ($user && $invite->canBeClaimedBy($user) && $invite->campaign->members()->where('user_id', $user->id)->exists()) {
            if ($invite->isPending()) {
                $invite->acceptFor($user);
            }

            return redirect()
                ->route('campaigns.show', $invite->campaign)
                ->with('success', 'You are already a member of this campaign.');
        }

        return view('invites.show', [
            'invite' => $invite,
            'matchingUser' => $user && $invite->canBeClaimedBy($user),
        ]);
    }

    public function accept(Request $request, CampaignInvite $invite): RedirectResponse
    {
        $invite->loadMissing('campaign');
        $user = $request->user();

        if (! $user || ! $invite->canBeClaimedBy($user)) {
            abort(403);
        }

        if ($invite->isExpired()) {
            $invite->markExpired();

            return redirect()
                ->route('invites.show', $invite->token)
                ->withErrors(['invite' => 'This invite has expired.']);
        }

        if ($invite->status !== CampaignInvite::STATUS_PENDING) {
            return redirect()
                ->route('invites.show', $invite->token)
                ->withErrors(['invite' => 'This invite is no longer active.']);
        }

        $invite->acceptFor($user);

        return redirect()
            ->route('campaigns.show', $invite->campaign)
            ->with('success', 'You have joined the campaign.');
    }

    public function decline(Request $request, CampaignInvite $invite): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $invite->canBeClaimedBy($user)) {
            abort(403);
        }

        if ($invite->isExpired()) {
            $invite->markExpired();

            return redirect()
                ->route('invites.show', $invite->token)
                ->withErrors(['invite' => 'This invite has expired.']);
        }

        if ($invite->status !== CampaignInvite::STATUS_PENDING) {
            return redirect()
                ->route('invites.show', $invite->token)
                ->withErrors(['invite' => 'This invite is no longer active.']);
        }

        $invite->declineFor($user);

        return redirect()
            ->route('invites.show', $invite->token)
            ->with('success', 'You declined the invitation.');
    }
}
