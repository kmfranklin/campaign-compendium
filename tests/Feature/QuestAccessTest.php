<?php

namespace Tests\Feature;

use App\Enums\QuestStatus;
use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Quest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_quest_routes(): void
    {
        $owner = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');
        $quest = $this->createQuest($campaign, 'Recover the Relic');

        $this->get(route('campaigns.quests.index', $campaign))
            ->assertRedirect(route('login'));

        $this->get(route('campaigns.quests.show', [$campaign, $quest]))
            ->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_view_quest_show_route(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Campaign A');
        $quest = $this->createQuest($campaign, 'Recover the Relic');

        $this->actingAs($outsider)
            ->get(route('campaigns.quests.show', [$campaign, $quest]))
            ->assertForbidden();
    }

    public function test_campaign_member_can_view_quest_show_route(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');
        $quest = $this->createQuest($campaign, 'Recover the Relic');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $this->actingAs($player)
            ->get(route('campaigns.quests.show', [$campaign, $quest]))
            ->assertOk()
            ->assertSee('Recover the Relic');
    }

    public function test_unverified_member_is_redirected_from_quest_routes(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->unverified()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');
        $quest = $this->createQuest($campaign, 'Recover the Relic');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $this->actingAs($player)
            ->get(route('campaigns.quests.show', [$campaign, $quest]))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_player_cannot_manage_quests(): void
    {
        $dm = User::factory()->create();
        $player = User::factory()->create();
        $campaign = $this->createDmCampaign($dm, 'Campaign A');
        $quest = $this->createQuest($campaign, 'Recover the Relic');

        $campaign->members()->attach($player->id, ['role_id' => Role::PLAYER]);

        $this->actingAs($player)
            ->get(route('campaigns.quests.create', $campaign))
            ->assertForbidden();

        $this->actingAs($player)
            ->post(route('campaigns.quests.store', $campaign), [
                'title' => 'Player Quest',
                'status' => QuestStatus::Planned->value,
            ])
            ->assertForbidden();

        $this->actingAs($player)
            ->get(route('campaigns.quests.edit', [$campaign, $quest]))
            ->assertForbidden();

        $this->actingAs($player)
            ->put(route('campaigns.quests.update', [$campaign, $quest]), [
                'title' => 'Updated Title',
                'status' => QuestStatus::Completed->value,
            ])
            ->assertForbidden();

        $this->actingAs($player)
            ->delete(route('campaigns.quests.destroy', [$campaign, $quest]))
            ->assertForbidden();
    }

    public function test_quest_route_returns_not_found_when_quest_belongs_to_another_campaign(): void
    {
        $user = User::factory()->create();
        $campaignA = $this->createDmCampaign($user, 'Campaign A');
        $campaignB = $this->createDmCampaign($user, 'Campaign B');
        $foreignQuest = $this->createQuest($campaignB, 'Into the Other Mists');

        $this->actingAs($user)
            ->get(route('campaigns.quests.show', [$campaignA, $foreignQuest]))
            ->assertNotFound();
    }

    public function test_cannot_attach_npc_from_a_different_campaign_to_a_quest(): void
    {
        $user = User::factory()->create();
        $campaignA = $this->createDmCampaign($user, 'Campaign A');
        $campaignB = $this->createDmCampaign($user, 'Campaign B');
        $quest = $this->createQuest($campaignA, 'Recover the Relic');

        $foreignNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaignB->id,
            'name' => 'Frank the Fighter',
        ]);

        $this->actingAs($user)
            ->post(route('campaigns.quests.npcs.attach', [$campaignA, $quest]), [
                'npc_id' => $foreignNpc->id,
                'role' => 'ally',
            ])
            ->assertForbidden();

        $this->assertCount(0, $quest->fresh()->npcs);
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

    private function createQuest(Campaign $campaign, string $title): Quest
    {
        return Quest::create([
            'campaign_id' => $campaign->id,
            'title' => $title,
            'description' => null,
            'notes' => null,
            'status' => QuestStatus::Active,
        ]);
    }
}
