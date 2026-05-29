<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemGuestCtaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_sign_in_cta_on_public_item_index_and_not_new_item_link(): void
    {
        $this->createItem([
            'name' => 'Longsword',
            'is_srd' => true,
        ]);

        $response = $this->get(route('items.index'));

        $response->assertOk();
        $response->assertSee('Sign in to create items');
        $response->assertSee('Sign in to clone');
        $response->assertDontSee('+ New Item', false);
    }

    public function test_guest_sees_sign_in_to_clone_for_srd_item_show(): void
    {
        $item = $this->createItem([
            'name' => 'Longsword',
            'is_srd' => true,
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertOk();
        $response->assertSee('Sign in to clone');
    }

    public function test_guest_item_index_hides_custom_items_they_cannot_open(): void
    {
        $owner = User::factory()->create();
        $this->createItem([
            'name' => 'Longsword',
            'is_srd' => true,
        ]);
        $this->createItem([
            'name' => 'House Blade',
            'is_srd' => false,
            'user_id' => $owner->id,
        ]);

        $response = $this->get(route('items.index'));

        $response->assertOk();
        $response->assertSee('Longsword');
        $response->assertDontSee('House Blade');
    }

    public function test_authenticated_user_only_sees_their_own_custom_items_in_item_index(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $this->createItem([
            'name' => 'House Blade',
            'is_srd' => false,
            'user_id' => $owner->id,
        ]);
        $this->createItem([
            'name' => 'Viewer Blade',
            'is_srd' => false,
            'user_id' => $viewer->id,
        ]);

        $response = $this->actingAs($viewer)->get(route('items.index'));

        $response->assertOk();
        $response->assertSee('Viewer Blade');
        $response->assertDontSee('House Blade');
    }

    public function test_non_owner_cannot_view_custom_item_show(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $item = $this->createItem([
            'name' => 'House Blade',
            'is_srd' => false,
            'user_id' => $owner->id,
        ]);

        $this->actingAs($viewer)
            ->get(route('items.show', $item))
            ->assertForbidden();
    }

    public function test_owner_still_sees_clone_for_custom_item_show(): void
    {
        $owner = User::factory()->create();
        $item = $this->createItem([
            'name' => 'House Blade',
            'is_srd' => false,
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->get(route('items.show', $item));

        $response->assertOk();
        $response->assertSee('Clone');
    }

    private function createItem(array $overrides = []): Item
    {
        return Item::create(array_merge([
            'item_key' => (string) Str::uuid(),
            'name' => 'Test Item',
            'description' => 'Test description',
            'is_srd' => true,
        ], $overrides));
    }
}
