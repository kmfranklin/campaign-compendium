<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemRaritiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/ItemRarity.json');
        $json = json_decode(file_get_contents($path), true);

        $rarities = collect($json)->map(function ($entry) {
            return [
                'slug' => $entry['pk'],                  // e.g. "common"
                'name' => $entry['fields']['name'],      // e.g. "Common"
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('item_rarities')->upsert(
            $rarities,
            ['slug'],
            ['name', 'updated_at']
        );
    }
}
