<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Merge all equipment sources into the items table
        $files = [
            base_path('database/data/Item.json'),
            base_path('database/data/Weapon.json'),
            base_path('database/data/Armor.json'),
        ];

        $items = collect($files)
            ->flatMap(fn($path) => json_decode(file_get_contents($path), true))
            // use pk as the unique key
            ->keyBy('pk')
            ->map(function ($entry) {
                $fields = $entry['fields'];

                return [
                    'item_key' => $entry['pk'],
                    'name' => $fields['name'] ?? null,
                    'description' => $fields['desc'] ?? null,
                    'cost' => $fields['cost'] ?? null,
                    'weight' => $fields['weight'] ?? null,
                    'is_magic_item' => $fields['is_magic_item'] ?? false,
                    'requires_attunement' => $fields['requires_attunement'] ?? false,
                    'attunement_requirements' => $fields['attunement'] ?? null,

                    'item_category_id' => DB::table('item_categories')
                        ->where('slug', $fields['category'] ?? null)
                        ->value('id'),

                    'item_rarity_id' => DB::table('item_rarities')
                        ->where('slug', $fields['rarity'] ?? null)
                        ->value('id'),

                    'armor_key' => $fields['armor'] ?? null,
                    'weapon_key' => $fields['weapon'] ?? null,

                    'is_srd' => true,
                    'user_id' => null,
                    'deleted_at' => null,

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values() // reset numeric keys
            ->all();

        foreach (array_chunk($items, 100) as $chunk) {
            DB::table('items')->upsert(
                $chunk,
                ['item_key'],
                [
                    'name',
                    'description',
                    'cost',
                    'weight',
                    'is_magic_item',
                    'requires_attunement',
                    'attunement_requirements',
                    'item_category_id',
                    'item_rarity_id',
                    'armor_key',
                    'weapon_key',
                    'is_srd',
                    'user_id',
                    'deleted_at',
                    'updated_at',
                ]
            );
        }
    }
}
