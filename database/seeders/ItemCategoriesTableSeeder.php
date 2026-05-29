<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/ItemCategory.json');
        $json = json_decode(file_get_contents($path), true);

        $categories = collect($json)->map(function ($entry) {
            return [
                'slug' => $entry['pk'],
                'name' => $entry['fields']['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('item_categories')->upsert(
            $categories,
            ['slug'],
            ['name', 'updated_at']
        );
    }
}
