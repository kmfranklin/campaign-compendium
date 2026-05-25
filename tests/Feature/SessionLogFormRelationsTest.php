<?php

namespace Tests\Feature;

use App\Enums\QuestStatus;
use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Quest;
use App\Models\Role;
use App\Models\SessionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionLogFormRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_create_saves_selected_npcs_and_quests(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createDmCampaign($user);

        $assignedNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'name' => 'Darla the Druid',
        ]);

        $unassignedNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => null,
            'name' => 'Carl the Cleric',
        ]);

        $quest = Quest::create([
            'campaign_id' => $campaign->id,
            'title' => 'Recover the Relic',
            'description' => 'Find the lost relic in the marsh.',
            'status' => QuestStatus::Active,
        ]);

        $response = $this->actingAs($user)->post(route('campaigns.sessions.store', $campaign), [
            'title' => 'Session 7: Marsh Echoes',
            'session_date' => '2026-05-25',
            'summary' => 'The party found new clues.',
            'npc_ids' => [$assignedNpc->id, $unassignedNpc->id],
            'quest_ids' => [$quest->id],
        ]);

        $sessionLog = SessionLog::first();

        $response->assertRedirect(route('campaigns.sessions.show', [$campaign, $sessionLog]));
        $this->assertNotNull($sessionLog);
        $this->assertEqualsCanonicalizing(
            [$assignedNpc->id, $unassignedNpc->id],
            $sessionLog->npcs()->pluck('npcs.id')->all()
        );
        $this->assertEquals([$quest->id], $sessionLog->quests()->pluck('quests.id')->all());
    }

    public function test_selecting_unassigned_npc_claims_it_for_the_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createDmCampaign($user);

        $npc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => null,
            'name' => 'Carl the Cleric',
        ]);

        $this->actingAs($user)->post(route('campaigns.sessions.store', $campaign), [
            'title' => 'Session 8: The Return',
            'session_date' => '2026-05-26',
            'summary' => 'Carl made his first appearance.',
            'npc_ids' => [$npc->id],
        ])->assertRedirect();

        $this->assertSame($campaign->id, $npc->fresh()->campaign_id);
    }

    public function test_session_edit_updates_selected_npcs_and_quests(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createDmCampaign($user);

        $firstNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'name' => 'Darla the Druid',
        ]);

        $secondNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => null,
            'name' => 'Carl the Cleric',
        ]);

        $firstQuest = Quest::create([
            'campaign_id' => $campaign->id,
            'title' => 'Recover the Relic',
            'description' => null,
            'status' => QuestStatus::Active,
        ]);

        $secondQuest = Quest::create([
            'campaign_id' => $campaign->id,
            'title' => 'Secure the Bridge',
            'description' => null,
            'status' => QuestStatus::Planned,
        ]);

        $sessionLog = $campaign->sessionLogs()->create([
            'title' => 'Session 6',
            'session_date' => '2026-05-20',
            'summary' => 'Initial draft.',
        ]);

        $sessionLog->npcs()->sync([$firstNpc->id]);
        $sessionLog->quests()->sync([$firstQuest->id]);

        $response = $this->actingAs($user)->put(route('campaigns.sessions.update', [$campaign, $sessionLog]), [
            'title' => 'Session 6 Revised',
            'session_date' => '2026-05-20',
            'summary' => 'Updated draft.',
            'npc_ids' => [$secondNpc->id],
            'quest_ids' => [$secondQuest->id],
        ]);

        $response->assertRedirect(route('campaigns.sessions.show', [$campaign, $sessionLog]));
        $this->assertEquals([$secondNpc->id], $sessionLog->fresh()->npcs()->pluck('npcs.id')->all());
        $this->assertEquals([$secondQuest->id], $sessionLog->fresh()->quests()->pluck('quests.id')->all());
        $this->assertSame($campaign->id, $secondNpc->fresh()->campaign_id);
    }

    public function test_session_route_returns_not_found_when_session_belongs_to_another_campaign(): void
    {
        $user = User::factory()->create();
        $campaignA = $this->createDmCampaign($user, 'Campaign A');
        $campaignB = $this->createDmCampaign($user, 'Campaign B');

        $sessionLog = $campaignB->sessionLogs()->create([
            'title' => 'Wrong Campaign Session',
            'session_date' => '2026-05-27',
            'summary' => 'Should not load under Campaign A.',
        ]);

        $this->actingAs($user)
            ->get(route('campaigns.sessions.show', [$campaignA, $sessionLog]))
            ->assertNotFound();
    }

    public function test_cannot_attach_npc_from_a_different_campaign_to_session(): void
    {
        $user = User::factory()->create();
        $campaignA = $this->createDmCampaign($user, 'Campaign A');
        $campaignB = $this->createDmCampaign($user, 'Campaign B');

        $sessionLog = $campaignA->sessionLogs()->create([
            'title' => 'Session A',
            'session_date' => '2026-05-27',
            'summary' => null,
        ]);

        $foreignNpc = Npc::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaignB->id,
            'name' => 'Frank the Fighter',
        ]);

        $this->actingAs($user)
            ->post(route('campaigns.sessions.npcs.attach', [$campaignA, $sessionLog, $foreignNpc]))
            ->assertForbidden();

        $this->assertCount(0, $sessionLog->fresh()->npcs);
    }

    public function test_cannot_attach_quest_from_a_different_campaign_to_session(): void
    {
        $user = User::factory()->create();
        $campaignA = $this->createDmCampaign($user, 'Campaign A');
        $campaignB = $this->createDmCampaign($user, 'Campaign B');

        $sessionLog = $campaignA->sessionLogs()->create([
            'title' => 'Session A',
            'session_date' => '2026-05-27',
            'summary' => null,
        ]);

        $foreignQuest = Quest::create([
            'campaign_id' => $campaignB->id,
            'title' => 'Into the Other Mists',
            'description' => null,
            'status' => QuestStatus::Planned,
        ]);

        $this->actingAs($user)
            ->post(route('campaigns.sessions.quests.attach', [$campaignA, $sessionLog, $foreignQuest]))
            ->assertForbidden();

        $this->assertCount(0, $sessionLog->fresh()->quests);
    }

    private function createDmCampaign(User $user, string $name = 'Campaign A'): Campaign
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
