<?php

namespace Tests\Feature;

use App\Models\Creature;
use App\Models\Item;
use App\Models\Spell;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexBackLinkPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_spell_index_links_preserve_query_string_for_show_back_link(): void
    {
        $spell = null;
        foreach (range(1, 21) as $number) {
            $created = Spell::create([
                'name' => "Burn Spell {$number}",
                'level' => 1,
                'description' => 'A sheet of flames shoots forth.',
                'is_srd' => true,
            ]);

            if ($number === 21) {
                $spell = $created;
            }
        }

        $indexUrl = route('spells.index', [
            'page' => 2,
            'q' => 'burn spell',
        ]);
        $showUrl = route('spells.show', [
            'spell' => $spell,
            'page' => 2,
            'q' => 'burn spell',
        ]);

        $indexResponse = $this->get($indexUrl);
        $indexResponse->assertOk();
        $this->assertStringContainsString('?page=2&amp;q=burn%20spell', $indexResponse->getContent());

        $showResponse = $this->get($showUrl);
        $showResponse->assertOk();
        $this->assertStringContainsString('href="' . e($indexUrl) . '"', $showResponse->getContent());
    }

    public function test_monster_index_links_preserve_query_string_for_show_back_link(): void
    {
        $creature = null;
        foreach (range(1, 21) as $number) {
            $created = Creature::create([
                'name' => "Kraken {$number}",
                'size' => 'gargantuan',
                'challenge_rating' => '23',
                'is_srd' => true,
            ]);

            if ($number === 21) {
                $creature = $created;
            }
        }

        $indexUrl = route('creatures.index', [
            'page' => 2,
            'size' => 'gargantuan',
        ]);
        $showUrl = route('creatures.show', [
            'creature' => $creature,
            'page' => 2,
            'size' => 'gargantuan',
        ]);

        $indexResponse = $this->get($indexUrl);
        $indexResponse->assertOk();
        $this->assertStringContainsString('?page=2&amp;size=gargantuan', $indexResponse->getContent());

        $showResponse = $this->get($showUrl);
        $showResponse->assertOk();
        $this->assertStringContainsString('href="' . e($indexUrl) . '"', $showResponse->getContent());
    }

    public function test_item_index_links_preserve_query_string_for_show_back_link(): void
    {
        $item = null;
        foreach (range(1, 16) as $number) {
            $created = Item::create([
                'item_key' => (string) Str::uuid(),
                'name' => "Long Blade {$number}",
                'description' => 'A martial melee weapon.',
                'is_srd' => true,
            ]);

            if ($number === 16) {
                $item = $created;
            }
        }

        $indexUrl = route('items.index', [
            'page' => 2,
            'q' => 'long',
        ]);
        $showUrl = route('items.show', [
            'item' => $item,
            'from' => 'all',
            'page' => 2,
            'q' => 'long',
        ]);

        $indexResponse = $this->get($indexUrl);
        $indexResponse->assertOk();
        $this->assertStringContainsString('?from=all&amp;page=2&amp;q=long', $indexResponse->getContent());

        $showResponse = $this->get($showUrl);
        $showResponse->assertOk();
        $this->assertStringContainsString('href="' . e($indexUrl) . '"', $showResponse->getContent());
    }
}
