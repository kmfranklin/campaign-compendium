<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Role;
use App\Models\SessionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SessionLogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_session_log_and_media_routes(): void
    {
        Storage::fake('private');

        $owner = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');
        $sessionLog = $campaign->sessionLogs()->create([
            'title' => 'Session 9',
            'session_date' => '2026-05-28',
            'summary' => 'Guests should not be able to see this.',
        ]);

        $media = $this->createMediaForSession($sessionLog);

        $this->get(route('campaigns.sessions.show', [$campaign, $sessionLog]))
            ->assertRedirect(route('login'));

        $this->get(route('media.show', $media))
            ->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_view_session_log_or_media(): void
    {
        Storage::fake('private');

        $owner = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');
        $sessionLog = $campaign->sessionLogs()->create([
            'title' => 'Session 10',
            'session_date' => '2026-05-28',
            'summary' => 'Private to campaign members.',
        ]);

        $media = $this->createMediaForSession($sessionLog);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('campaigns.sessions.show', [$campaign, $sessionLog]))
            ->assertForbidden();

        $this->actingAs($outsider)
            ->get(route('media.show', $media))
            ->assertForbidden();
    }

    public function test_campaign_member_can_view_private_media(): void
    {
        Storage::fake('private');

        $dm = User::factory()->create();
        $player = User::factory()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');
        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $sessionLog = $campaign->sessionLogs()->create([
            'title' => 'Session 11',
            'session_date' => '2026-05-28',
            'summary' => 'Members can stream recordings.',
        ]);

        $media = $this->createMediaForSession($sessionLog);

        $this->actingAs($player)
            ->get(route('media.show', $media))
            ->assertOk()
            ->assertHeader('Content-Type', 'audio/mpeg');
    }

    private function createDmCampaign(User $user, string $name): Campaign
    {
        Role::firstOrCreate(['id' => Role::DM], ['name' => 'DM']);
        Role::firstOrCreate(['id' => Role::PLAYER], ['name' => 'Player']);

        $campaign = Campaign::create([
            'name' => $name,
            'description' => 'Test campaign',
            'dm_id' => $user->id,
        ]);

        $campaign->members()->attach($user->id, ['role_id' => Role::DM]);

        return $campaign;
    }

    private function createMediaForSession(SessionLog $sessionLog)
    {
        $path = "session-media/{$sessionLog->campaign_id}/{$sessionLog->id}/session.mp3";

        Storage::disk('private')->put($path, 'fake-audio');

        return $sessionLog->media()->create([
            'filename' => 'session.mp3',
            'path' => $path,
            'mime_type' => 'audio/mpeg',
            'size' => 10,
        ]);
    }
}
