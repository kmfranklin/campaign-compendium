<?php

namespace Tests\Feature\Compendium;

use App\Models\Campaign;
use App\Models\Npc;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NpcCampaignOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_user_can_create_unassigned_npc(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('compendium.npcs.store'), [
            'name' => 'Wren Hollowleaf',
        ]);

        $npc = Npc::first();

        $response->assertRedirect(route('compendium.npcs.show', $npc));
        $this->assertNotNull($npc);
        $this->assertSame($user->id, $npc->user_id);
        $this->assertNull($npc->campaign_id);
    }

    public function test_user_can_attach_npc_to_owned_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createDmCampaign($user, 'Misty Vale');

        $response = $this->actingAs($user)->post(route('compendium.npcs.store'), [
            'name' => 'Captain Rowan',
            'campaign_id' => $campaign->id,
        ]);

        $npc = Npc::first();

        $response->assertRedirect(route('compendium.npcs.show', $npc));
        $this->assertNotNull($npc);
        $this->assertSame($campaign->id, $npc->campaign_id);
    }

    public function test_user_cannot_attach_npc_to_campaign_they_do_not_own(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $campaign = $this->createDmCampaign($owner, 'Misty Vale');

        $response = $this->actingAs($otherUser)->post(route('compendium.npcs.store'), [
            'name' => 'Captain Rowan',
            'campaign_id' => $campaign->id,
        ]);

        $response->assertSessionHasErrors('campaign_id');
        $this->assertDatabaseCount('npcs', 0);
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
