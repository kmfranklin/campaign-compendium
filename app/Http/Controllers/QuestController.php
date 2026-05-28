<?php

namespace App\Http\Controllers;

use App\Enums\QuestStatus;
use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class QuestController extends Controller
{
    /**
     * Abort unless the quest actually belongs to this campaign.
     */
    private function ensureQuestBelongsToCampaign(Campaign $campaign, Quest $quest): void
    {
        abort_unless($quest->campaign_id === $campaign->id, 404);
    }

    /**
     * Enforce the "same campaign or unassigned" NPC selection rule.
     */
    private function ensureNpcSelectableForCampaign(Campaign $campaign, Npc $npc): void
    {
        if ($npc->campaign_id !== null && $npc->campaign_id !== $campaign->id) {
            abort(403, 'That NPC belongs to a different campaign.');
        }
    }

    /**
     * Display a listing of the quests for a campaign.
     */
    public function index(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $quests = $campaign->quests()->latest()->get();

        return view('quests.index', compact('campaign', 'quests'));
    }

    /**
     * Show the form for creating a new quest.
     */
    public function create(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $statuses = QuestStatus::cases();

        return view('quests.create', compact('campaign', 'statuses'));
    }

    /**
     * Store a newly created quest in storage.
     */
    public function store(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
            'status'      => ['required', new Enum(QuestStatus::class)],
        ]);

        $campaign->quests()->create($validated);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Quest created successfully.');
    }

    /**
     * Display the specified quest.
     */
    public function show(Campaign $campaign, Quest $quest)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('view', $campaign);

        $attachedIds = $quest->npcs()->pluck('npcs.id');
        $availableNpcs = Npc::where('user_id', auth()->id())
            ->where(function ($query) use ($campaign) {
                $query->where('campaign_id', $campaign->id)
                    ->orWhereNull('campaign_id');
            })
            ->whereNotIn('id', $attachedIds)
            ->orderBy('name')
            ->get();

        return view('quests.show', compact('campaign', 'quest', 'availableNpcs'));
    }

    /**
     * Show the form for editing the specified quest.
     */
    public function edit(Campaign $campaign, Quest $quest)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('update', $campaign);

        $statuses = QuestStatus::cases();

        return view('quests.edit', compact('campaign', 'quest', 'statuses'));
    }

    /**
     * Update the specified quest in storage.
     */
    public function update(Request $request, Campaign $campaign, Quest $quest)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
            'status'      => ['required', new Enum(QuestStatus::class)],
        ]);

        $quest->update($validated);

        return redirect()
            ->route('campaigns.quests.show', [$campaign, $quest])
            ->with('success', 'Quest updated successfully.');
    }

    /**
     * Remove the specified quest from storage.
     */
    public function destroy(Campaign $campaign, Quest $quest)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('delete', $campaign);

        $quest->delete();

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Quest deleted successfully.');
    }

    /**
     * Attach NPC to quest.
     */
    public function attachNpc(Request $request, Campaign $campaign, Quest $quest)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'npc_id' => ['required', 'exists:npcs,id'],
            'role'   => ['nullable', 'string', 'max:50'],
        ]);

        $npc = Npc::where('user_id', auth()->id())->findOrFail($validated['npc_id']);

        $this->ensureNpcSelectableForCampaign($campaign, $npc);

        if ($npc->campaign_id === null) {
            $npc->campaign()->associate($campaign);
            $npc->save();
        }

        $quest->npcs()->syncWithoutDetaching([
            $npc->id => ['role' => $validated['role'] ?? null],
        ]);

        return redirect()
            ->route('campaigns.quests.show', [$campaign, $quest])
            ->with('success', 'NPC linked to quest.');
    }

    /**
     * Detach NPC from quest.
     */
    public function detachNpc(Campaign $campaign, Quest $quest, Npc $npc)
    {
        $this->ensureQuestBelongsToCampaign($campaign, $quest);
        $this->authorize('update', $campaign);

        $quest->npcs()->detach($npc->id);

        return redirect()
            ->route('campaigns.quests.show', [$campaign, $quest])
            ->with('success', 'NPC detached from quest.');
    }

}
