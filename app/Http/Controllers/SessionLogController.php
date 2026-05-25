<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Quest;
use App\Models\SessionLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SessionLogController extends Controller
{
    /**
     * Abort unless the session log actually belongs to this campaign.
     */
    private function ensureSessionBelongsToCampaign(Campaign $campaign, SessionLog $sessionLog): void
    {
        abort_unless($sessionLog->campaign_id === $campaign->id, 404);
    }

    /**
     * Abort unless the current user belongs to this campaign.
     */
    private function ensureCampaignMember(Campaign $campaign): void
    {
        $userId = auth()->id();

        $isMember = $campaign->dm_id === $userId
            || $campaign->members()->where('user_id', $userId)->exists();

        abort_unless($isMember, 403);
    }

    /**
     * Show the form for creating a new session log.
     */
    public function create(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $availableNpcs = $this->availableNpcsForCampaign($campaign);
        $availableQuests = $this->availableQuestsForCampaign($campaign);

        return view('session-logs.create', compact('campaign', 'availableNpcs', 'availableQuests'));
    }

    /**
     * Store a newly created session log.
     */
    public function store(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'summary'      => ['nullable', 'string'],
            'npc_ids'      => ['nullable', 'array'],
            'npc_ids.*'    => ['integer', 'distinct'],
            'quest_ids'    => ['nullable', 'array'],
            'quest_ids.*'  => ['integer', 'distinct'],
            // 200 MB limit; audio files can be large.
            // Ensure upload_max_filesize and post_max_size in php.ini match.
            'media'        => ['nullable', 'file', 'mimes:mp3,wav,ogg,flac,m4a,aac', 'max:204800'],
        ]);

        $selectedNpcs = $this->validateSelectedNpcs($campaign, $validated['npc_ids'] ?? []);
        $selectedQuests = $this->validateSelectedQuests($campaign, $validated['quest_ids'] ?? []);

        $sessionLog = $campaign->sessionLogs()->create([
            'title'        => $validated['title'],
            'session_date' => $validated['session_date'],
            'summary'      => $validated['summary'] ?? null,
        ]);

        $this->syncSessionNpcs($campaign, $sessionLog, $selectedNpcs);
        $sessionLog->quests()->sync($selectedQuests->modelKeys());

        if ($request->hasFile('media')) {
            $this->storeMedia($sessionLog, $request->file('media'));
        }

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'Session logged successfully.');
    }

    /**
     * Display the specified session log.
     */
    public function show(Campaign $campaign, SessionLog $sessionLog)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->ensureCampaignMember($campaign);

        $sessionLog->load(['media', 'npcs', 'quests']);

        $attachedNpcIds   = $sessionLog->npcs->pluck('id');
        $attachedQuestIds = $sessionLog->quests->pluck('id');

        // Offer campaign NPCs plus unassigned NPCs for later additions.
        $availableNpcs = $this->availableNpcsForCampaign($campaign)
            ->whereNotIn('id', $attachedNpcIds)
            ->values();
        $availableQuests = $this->availableQuestsForCampaign($campaign)
            ->whereNotIn('id', $attachedQuestIds)
            ->values();

        return view('session-logs.show', compact(
            'campaign',
            'sessionLog',
            'availableNpcs',
            'availableQuests',
        ));
    }

    /**
     * Show the form for editing the specified session log.
     */
    public function edit(Campaign $campaign, SessionLog $sessionLog)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $sessionLog->load(['media', 'npcs', 'quests']);

        $availableNpcs = $this->availableNpcsForCampaign($campaign);
        $availableQuests = $this->availableQuestsForCampaign($campaign);

        return view('session-logs.edit', compact('campaign', 'sessionLog', 'availableNpcs', 'availableQuests'));
    }

    /**
     * Update the specified session log.
     */
    public function update(Request $request, Campaign $campaign, SessionLog $sessionLog)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'session_date'  => ['required', 'date'],
            'summary'       => ['nullable', 'string'],
            'npc_ids'       => ['nullable', 'array'],
            'npc_ids.*'     => ['integer', 'distinct'],
            'quest_ids'     => ['nullable', 'array'],
            'quest_ids.*'   => ['integer', 'distinct'],
            'media'         => ['nullable', 'file', 'mimes:mp3,wav,ogg,flac,m4a,aac', 'max:204800'],
            'remove_media'  => ['nullable', 'boolean'],
        ]);

        $selectedNpcs = $this->validateSelectedNpcs($campaign, $validated['npc_ids'] ?? []);
        $selectedQuests = $this->validateSelectedQuests($campaign, $validated['quest_ids'] ?? []);

        $sessionLog->update([
            'title'        => $validated['title'],
            'session_date' => $validated['session_date'],
            'summary'      => $validated['summary'] ?? null,
        ]);

        $this->syncSessionNpcs($campaign, $sessionLog, $selectedNpcs);
        $sessionLog->quests()->sync($selectedQuests->modelKeys());

        // Delete existing media if user checked "remove recording"
        if ($request->boolean('remove_media')) {
            $this->deleteMedia($sessionLog);
        }

        // Replace existing media if a new file was uploaded
        if ($request->hasFile('media')) {
            $this->deleteMedia($sessionLog);
            $this->storeMedia($sessionLog, $request->file('media'));
        }

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'Session updated successfully.');
    }

    /**
     * Delete the specified session log.
     */
    public function destroy(Campaign $campaign, SessionLog $sessionLog)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $this->deleteMedia($sessionLog);
        $sessionLog->delete();

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('success', 'Session log deleted.');
    }

    /**
     * Attach an NPC to this session log.
     */
    public function attachNpc(Request $request, Campaign $campaign, SessionLog $sessionLog, Npc $npc)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $this->ensureNpcSelectableForCampaign($campaign, $npc);

        if ($npc->campaign_id === null) {
            $npc->campaign()->associate($campaign);
            $npc->save();
        }

        $sessionLog->npcs()->syncWithoutDetaching([$npc->id]);

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'NPC added to session.');
    }

    /**
     * Detach an NPC from this session log.
     */
    public function detachNpc(Campaign $campaign, SessionLog $sessionLog, Npc $npc)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $sessionLog->npcs()->detach($npc->id);

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'NPC removed from session.');
    }

    /**
     * Attach a quest to this session log.
     */
    public function attachQuest(Request $request, Campaign $campaign, SessionLog $sessionLog, Quest $quest)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $this->ensureQuestBelongsToCampaign($campaign, $quest);

        $sessionLog->quests()->syncWithoutDetaching([$quest->id]);

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'Quest marked as advanced in this session.');
    }

    /**
     * Detach a quest from this session log.
     */
    public function detachQuest(Campaign $campaign, SessionLog $sessionLog, Quest $quest)
    {
        $this->ensureSessionBelongsToCampaign($campaign, $sessionLog);
        $this->authorize('update', $campaign);

        $sessionLog->quests()->detach($quest->id);

        return redirect()
            ->route('campaigns.sessions.show', [$campaign, $sessionLog])
            ->with('success', 'Quest removed from session.');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Store an uploaded file and attach a Media record to the session log.
     */
    private function storeMedia(SessionLog $sessionLog, \Illuminate\Http\UploadedFile $file): void
    {
        $path = $file->store("session-media/{$sessionLog->campaign_id}/{$sessionLog->id}", 'private');

        $sessionLog->media()->create([
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);
    }

    /**
     * Delete the media file from disk and remove the Media record.
     */
    private function deleteMedia(SessionLog $sessionLog): void
    {
        $sessionLog->load('media');

        if ($sessionLog->media) {
            Storage::disk('private')->delete($sessionLog->media->path);
            $sessionLog->media->delete();
        }
    }

    /**
     * NPCs selectable on session forms: campaign-owned plus unassigned.
     */
    private function availableNpcsForCampaign(Campaign $campaign): Collection
    {
        return Npc::query()
            ->where('user_id', auth()->id())
            ->where(function ($query) use ($campaign) {
                $query->where('campaign_id', $campaign->id)
                    ->orWhereNull('campaign_id');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Quests selectable on session forms: only this campaign's quests.
     */
    private function availableQuestsForCampaign(Campaign $campaign): Collection
    {
        return $campaign->quests()->orderBy('title')->get();
    }

    /**
     * Validate selected NPC ids against the current campaign form rules.
     */
    private function validateSelectedNpcs(Campaign $campaign, array $npcIds): Collection
    {
        if (empty($npcIds)) {
            return new Collection();
        }

        $npcs = Npc::query()
            ->whereIn('id', $npcIds)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('id');

        foreach ($npcIds as $npcId) {
            $npc = $npcs->get($npcId);

            if (! $npc) {
                abort(422, 'One or more selected NPCs could not be found.');
            }

            $this->ensureNpcSelectableForCampaign($campaign, $npc);
        }

        return new Collection(
            collect($npcIds)
                ->map(fn (int $id) => $npcs->get($id))
                ->values()
                ->all()
        );
    }

    /**
     * Validate selected quests against the current campaign.
     */
    private function validateSelectedQuests(Campaign $campaign, array $questIds): Collection
    {
        if (empty($questIds)) {
            return new Collection();
        }

        $quests = Quest::query()
            ->whereIn('id', $questIds)
            ->get()
            ->keyBy('id');

        foreach ($questIds as $questId) {
            $quest = $quests->get($questId);

            if (! $quest) {
                abort(422, 'One or more selected quests could not be found.');
            }

            $this->ensureQuestBelongsToCampaign($campaign, $quest);
        }

        return new Collection(
            collect($questIds)
                ->map(fn (int $id) => $quests->get($id))
                ->values()
                ->all()
        );
    }

    /**
     * Sync selected NPCs and auto-claim any unassigned NPCs into the campaign.
     */
    private function syncSessionNpcs(Campaign $campaign, SessionLog $sessionLog, Collection $npcs): void
    {
        foreach ($npcs as $npc) {
            if ($npc->campaign_id === null) {
                $npc->campaign()->associate($campaign);
                $npc->save();
            }
        }

        $sessionLog->npcs()->sync($npcs->modelKeys());
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
     * Quests must always belong to the current campaign.
     */
    private function ensureQuestBelongsToCampaign(Campaign $campaign, Quest $quest): void
    {
        if ($quest->campaign_id !== $campaign->id) {
            abort(403, 'That quest belongs to a different campaign.');
        }
    }
}
