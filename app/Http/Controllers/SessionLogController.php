<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Quest;
use App\Models\SessionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SessionLogController extends Controller
{
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

        return view('session-logs.create', compact('campaign'));
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
            // 200 MB limit; audio files can be large.
            // Ensure upload_max_filesize and post_max_size in php.ini match.
            'media'        => ['nullable', 'file', 'mimes:mp3,wav,ogg,flac,m4a,aac', 'max:204800'],
        ]);

        $sessionLog = $campaign->sessionLogs()->create([
            'title'        => $validated['title'],
            'session_date' => $validated['session_date'],
            'summary'      => $validated['summary'] ?? null,
        ]);

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
        $this->ensureCampaignMember($campaign);

        $sessionLog->load(['media', 'npcs', 'quests']);

        $attachedNpcIds   = $sessionLog->npcs->pluck('id');
        $attachedQuestIds = $sessionLog->quests->pluck('id');

        // Only show NPCs and quests that belong to this campaign
        $availableNpcs   = $campaign->npcs()->whereNotIn('id', $attachedNpcIds)->orderBy('name')->get();
        $availableQuests = $campaign->quests()->whereNotIn('id', $attachedQuestIds)->orderBy('title')->get();

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
        $this->authorize('update', $campaign);

        $sessionLog->load('media');

        return view('session-logs.edit', compact('campaign', 'sessionLog'));
    }

    /**
     * Update the specified session log.
     */
    public function update(Request $request, Campaign $campaign, SessionLog $sessionLog)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'session_date'  => ['required', 'date'],
            'summary'       => ['nullable', 'string'],
            'media'         => ['nullable', 'file', 'mimes:mp3,wav,ogg,flac,m4a,aac', 'max:204800'],
            'remove_media'  => ['nullable', 'boolean'],
        ]);

        $sessionLog->update([
            'title'        => $validated['title'],
            'session_date' => $validated['session_date'],
            'summary'      => $validated['summary'] ?? null,
        ]);

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
        $this->authorize('update', $campaign);

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
        $this->authorize('update', $campaign);

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
}
