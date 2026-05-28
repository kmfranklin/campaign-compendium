<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignInvite;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_dashboard_shows_first_run_campaign_prompt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Create your first campaign')
            ->assertSee('Create Campaign')
            ->assertSee('Browse Spells');
    }

    public function test_dashboard_surfaces_pending_invites_summary(): void
    {
        $dm = User::factory()->create(['name' => 'Dungeon Master']);
        $user = User::factory()->create(['email' => 'player@example.com']);
        Role::firstOrCreate(['id' => Role::DM], ['name' => 'DM']);

        $campaign = Campaign::create([
            'name' => 'Misty Vale',
            'description' => 'Test campaign',
            'dm_id' => $dm->id,
        ]);

        $campaign->members()->attach($dm->id, ['role_id' => Role::DM]);

        $invite = CampaignInvite::create([
            'campaign_id' => $campaign->id,
            'inviter_id' => $dm->id,
            'invitee_id' => $user->id,
            'email' => $user->email,
            'token' => 'dashboard-invite-token',
            'status' => CampaignInvite::STATUS_PENDING,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => Notification::TYPE_INVITE,
            'notifiable_type' => CampaignInvite::class,
            'notifiable_id' => $invite->id,
            'data' => [
                'campaign_name' => 'Misty Vale',
                'inviter_name' => 'Dungeon Master',
            ],
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pending Invites')
            ->assertSee('Review Invites');
    }
}
