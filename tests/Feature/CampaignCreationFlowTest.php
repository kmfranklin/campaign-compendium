<?php

namespace Tests\Feature;

use App\Mail\CampaignInviteMail;
use App\Models\Campaign;
use App\Models\CampaignInvite;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CampaignCreationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dm_can_create_campaign_and_queue_player_invites(): void
    {
        Mail::fake();

        $dm = User::factory()->create([
            'name' => 'Dungeon Master',
            'email_verified_at' => now(),
        ]);
        $player = User::factory()->create(['email' => 'player@example.com']);

        Role::firstOrCreate(['id' => Role::DM], ['name' => 'DM']);
        Role::firstOrCreate(['id' => Role::PLAYER], ['name' => 'Player']);

        $response = $this->actingAs($dm)->post(route('campaigns.store'), [
            'name' => 'The Ember Coast',
            'description' => 'A pirate campaign with occult storms.',
            'invite_emails' => [
                'player@example.com',
                'newfriend@example.com',
                $dm->email,
            ],
        ]);

        $campaign = Campaign::where('name', 'The Ember Coast')->first();

        $response->assertRedirect(route('campaigns.show', $campaign));
        $this->assertNotNull($campaign);
        $this->assertSame($dm->id, $campaign->dm_id);
        $this->assertTrue($campaign->members()->where('user_id', $dm->id)->exists());

        $this->assertDatabaseCount('campaign_invites', 2);
        $this->assertDatabaseHas('campaign_invites', [
            'campaign_id' => $campaign->id,
            'email' => 'player@example.com',
            'invitee_id' => $player->id,
            'status' => CampaignInvite::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('campaign_invites', [
            'campaign_id' => $campaign->id,
            'email' => 'newfriend@example.com',
            'status' => CampaignInvite::STATUS_PENDING,
        ]);

        Mail::assertQueued(CampaignInviteMail::class, 2);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $player->id,
            'type' => Notification::TYPE_INVITE,
            'notifiable_type' => CampaignInvite::class,
        ]);
    }
}
