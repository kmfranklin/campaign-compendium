<?php

namespace Tests\Feature\Compendium;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Npc;

class NpcClassFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_filter_returns_only_matching_npcs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Npc::factory()->create(['user_id' => $user->id, 'class' => 'Wizard']);
        Npc::factory()->create(['user_id' => $user->id, 'class' => 'Rogue']);

        // 1) Hit the filtered index
        $response = $this->get('/compendium/npcs?class=Wizard')
                         ->assertStatus(200)
                         ->assertSee('Wizard');

        // 2) Grab the 'npcs' variable passed to the view
        $npcs = $response->viewData('npcs');

        // 3) Assert there’s exactly one NPC returned
        $this->assertCount(1, $npcs);

        // 4) And that its class is 'Wizard'
        $this->assertEquals('Wizard', $npcs->first()->class);
    }
}
