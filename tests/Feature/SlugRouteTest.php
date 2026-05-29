<?php

namespace Tests\Feature;

use App\Enums\QuestStatus;
use App\Models\Campaign;
use App\Models\Creature;
use App\Models\Item;
use App\Models\Quest;
use App\Models\Role;
use App\Models\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SlugRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_srd_routes_use_human_readable_slugs(): void
    {
        $spell = Spell::create([
            'name' => 'Magic Missile',
            'level' => 1,
            'is_srd' => true,
        ]);

        $creature = Creature::create([
            'name' => 'Adult Black Dragon',
            'is_srd' => true,
        ]);

        $item = Item::create([
            'item_key' => (string) Str::uuid(),
            'name' => 'Potion of Healing',
            'is_srd' => true,
        ]);

        $this->assertStringEndsWith('/spells/magic-missile', route('spells.show', $spell));
        $this->assertStringEndsWith('/monsters/adult-black-dragon', route('creatures.show', $creature));
        $this->assertStringEndsWith('/items/potion-of-healing', route('items.show', $item));

        $this->get(route('spells.show', $spell))->assertOk();
        $this->get(route('creatures.show', $creature))->assertOk();
        $this->get(route('items.show', $item))->assertOk();
    }

    public function test_campaign_and_quest_routes_use_slugs(): void
    {
        $user = User::factory()->create();
        $campaign = $this->createDmCampaign($user, 'Curse of Strahd');
        $quest = Quest::create([
            'campaign_id' => $campaign->id,
            'title' => 'Find Ireena',
            'status' => QuestStatus::Active,
        ]);

        $this->assertStringEndsWith('/campaigns/curse-of-strahd', route('campaigns.show', $campaign));
        $this->assertStringEndsWith('/campaigns/curse-of-strahd/quests/find-ireena', route('campaigns.quests.show', [$campaign, $quest]));

        $this->actingAs($user)->get(route('campaigns.show', $campaign))->assertOk();
        $this->actingAs($user)->get(route('campaigns.quests.show', [$campaign, $quest]))->assertOk();
    }

    public function test_legacy_numeric_urls_still_resolve_after_slug_rollout(): void
    {
        $spell = Spell::create([
            'name' => 'Shield',
            'level' => 1,
            'is_srd' => true,
        ]);

        $this->get("/spells/{$spell->id}")
            ->assertOk()
            ->assertSee('Shield');
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
