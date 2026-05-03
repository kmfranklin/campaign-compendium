<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use Illuminate\Support\Facades\Auth;
class CampaignController extends Controller
{
    /**
     * Map Campaign resource methods to their corresponding policy checks.
     * - Ensures index, show, create, store, edit, update, and destroy
     *   automatically call the correct CampaignPolicy method.
     * - Keeps authorization logic centralized and consistent.
     */
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Campaign::class, 'campaign');
    }

    /**
     * Display a listing of the campaigns the user can see.
     * - Later: paginate, filter, and scope by membership/visibility.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only show campaigns the authenticated user is a member of (DM or player).
        // This scopes the list to their own campaigns rather than every campaign
        // in the database, which would be a privacy/data leak.
        $campaigns = Campaign::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->paginate(10);

        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Show the form for creating a new campaign.
     * - DM-only: enforce via CampaignPolicy or gate in controller temporarily.
     */
    public function create()
    {
        return view('campaigns.create');
    }

    /**
     * Store a newly created campaign in storage.
     * - Uses StoreCampaignRequest for validation (rules TBD).
     * - Sets current user as DM/owner in pivot/user role.
     */
    public function store(StoreCampaignRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $campaign = Campaign::create([
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'dm_id'       => $user->id,
        ]);

        $campaign->members()->attach($user->id, ['role_id' => Role::DM]);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign created successfully.');
    }

    /**
     * Display the specified campaign.
     * - Show core campaign data and membership status.
     */
    public function show(Campaign $campaign)
    {
        $quests = $campaign->quests()->latest()->get();

        $npcIds = $campaign->quests()
            ->with('npcs')
            ->get()
            ->pluck('npcs')
            ->flatten()
            ->unique('id')
            ->values();

        return view('campaigns.show', compact('campaign', 'quests', 'npcIds'));
    }

    /**
     * Show the form for editing the specified campaign.
     * - DM-only: enforce via CampaignPolicy.
     */
    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified campaign in storage.
     * - Validate via FormRequest; authorize via policy.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $campaign->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    /**
     * Remove the specified campaign from storage.
     * - DM-only: soft-delete or hard-delete per product choice (decide later).
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()
            ->route('campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Add a member to the campaign.
     */
    public function addMember(Request $request, Campaign $campaign)
    {
        $this->authorize('addMember', $campaign);

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $targetUser = \App\Models\User::where('email', $request->email)->first();

        if ($campaign->members()->where('user_id', $targetUser->id)->exists()) {
            return back()->withErrors([
                'email' => 'That user is already a member of this campaign.',
            ]);
        }

        $campaign->members()->attach($targetUser->id, [
            'role_id' => \App\Models\Role::PLAYER,
        ]);

        return back()->with('success', 'Member added successfully.');
    }

public function removeMember(Request $request, Campaign $campaign)
{
    $this->authorize('removeMember', $campaign);

    $request->validate([
        'user_id' => ['required', 'exists:users,id'],
    ]);

    $targetUser = \App\Models\User::find($request->user_id);

    if (! $campaign->members()->where('user_id', $targetUser->id)->exists()) {
        return back()->withErrors([
            'user_id' => 'That user is not a member of this campaign.',
        ]);
    }

    // Prevent removing the DM
    if ($campaign->members()
        ->where('user_id', $targetUser->id)
        ->wherePivot('role_id', \App\Models\Role::DM)
        ->exists()
    ) {
        return back()->withErrors([
            'user_id' => 'The DM cannot be removed from the campaign.',
        ]);
    }

    $campaign->members()->detach($targetUser->id);

    return back()->with('success', 'Member removed successfully.');
}
}
