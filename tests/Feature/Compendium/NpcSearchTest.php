<?php

namespace Tests\Feature\Compendium;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Npc;
use App\Models\User;

class NpcSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_search_filters_npcs()
    {
        // 1) Create and sign in a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2) Seed two NPCs
        Npc::factory()->create(['user_id' => $user->id, 'name' => 'Gandalf']);
        Npc::factory()->create(['user_id' => $user->id, 'name' => 'Frodo']);

        // 3) Hit the route as an authenticated user
        $this->get('/compendium/npcs?q=Gandalf')
             ->assertStatus(200)
             ->assertSee('Gandalf')
             ->assertDontSee('Frodo');
    }
}
