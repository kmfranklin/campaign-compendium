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
     * Display a listing of the quests for a campaign.
     */
    public function index(Campaign $campaign)
    {
        $quests = $campaign->quests()->latest()->get();

        return view('quests.index', compact('campaign', 'quests'));
    }

    /**
     * Show the form for creating a new quest.
     */
    public function create(Campaign $campaign)
    {
        $statuses = QuestStatus::cases();

        return view('quests.create', compact('campaign', 'statuses'));
    }

    /**
     * Store a newly created quest in storage.
     */
    public function store(Request $request, Campaign $campaign)
    {
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
        $attachedIds = $quest->npcs()->pluck('npcs.id');
        $availableNpcs = Npc::where('user_id', auth()->id())
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
        $statuses = QuestStatus::cases();

        return view('quests.edit', compact('campaign', 'quest', 'statuses'));
    }

    /**
     * Update the specified quest in storage.
     */
    public function update(Request $request, Campaign $campaign, Quest $quest)
    {
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
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'npc_id' => ['required', 'exists:npcs,id'],
            'role'   => ['nullable', 'string', 'max:50'],
        ]);

        $quest->npcs()->syncWithoutDetaching([
            $validated['npc_id'] => ['role' => $validated['role'] ?? null],
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
        $this->authorize('update', $campaign);

        $quest->npcs()->detach($npc->id);

        return redirect()
            ->route('campaigns.quests.show', [$campaign, $quest])
            ->with('success', 'NPC detached from quest.');
    }

}
