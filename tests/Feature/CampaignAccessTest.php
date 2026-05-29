<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_campaign_routes(): void
    {
        $owner = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');

        $this->get(route('campaigns.index'))
            ->assertRedirect(route('login'));

        $this->get(route('campaigns.show', $campaign))
            ->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_view_private_campaign(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');

        $this->actingAs($outsider)
            ->get(route('campaigns.show', $campaign))
            ->assertForbidden();
    }

    public function test_campaign_member_can_view_private_campaign(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $this->actingAs($player)
            ->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('Campaign A');
    }

    public function test_campaign_index_only_lists_memberships_for_current_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $visibleCampaign = $this->createDmCampaign($user, 'Visible Campaign');
        $hiddenCampaign = $this->createDmCampaign($otherUser, 'Hidden Campaign');

        $response = $this->actingAs($user)
            ->get(route('campaigns.index'));

        $response->assertOk();
        $response->assertSee('Visible Campaign');
        $response->assertDontSee('Hidden Campaign');

        $this->assertTrue($visibleCampaign->members()->where('user_id', $user->id)->exists());
        $this->assertFalse($hiddenCampaign->members()->where('user_id', $user->id)->exists());
    }

    public function test_unverified_member_is_redirected_from_private_campaign_routes(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->unverified()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $this->actingAs($player)
            ->get(route('campaigns.show', $campaign))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_player_does_not_see_dm_only_campaign_controls(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);
        $campaign->quests()->create([
            'title' => 'Recover the Relic',
            'description' => null,
            'notes' => 'Secret DM notes',
            'status' => \App\Enums\QuestStatus::Active,
        ]);

        $response = $this->actingAs($player)->get(route('campaigns.show', $campaign));

        $response->assertOk();
        $response->assertDontSee('+ Add quest', false);
        $response->assertDontSee('+ Add NPC', false);
        $response->assertDontSee('Edit', false);
        $response->assertDontSee('Delete', false);
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
}
