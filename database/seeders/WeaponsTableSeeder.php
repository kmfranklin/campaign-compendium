<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeaponsTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/Weapon.json');
        $json = json_decode(file_get_contents($path), true);
        $itemIds = DB::table('items')->pluck('id', 'item_key');
        $damageTypeIds = DB::table('damage_types')->pluck('id', 'slug');

        $weapons = collect($json)->map(function ($entry) use ($itemIds, $damageTypeIds) {
            $fields = $entry['fields'];

            return [
                // Link back to parent item by pk
                'item_id' => $itemIds[$entry['pk']] ?? null,

                'damage_dice' => $fields['damage_dice'] ?? null,
                'damage_type_id' => $damageTypeIds[$fields['damage_type'] ?? ''] ?? null,

                'range' => $fields['range'] ?? null,
                'long_range' => $fields['long_range'] ?? null,
                'distance_unit' => $fields['distance_unit'] ?? null,

                'is_improvised' => $fields['is_improvised'] ?? false,
                'is_simple' => $fields['is_simple'] ?? false,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        })
        ->filter(fn ($weapon) => $weapon['item_id'] !== null) // skip if no parent item
        ->all();

        foreach (array_chunk($weapons, 100) as $chunk) {
            DB::table('weapons')->upsert(
                $chunk,
                ['item_id'],
                [
                    'damage_dice',
                    'damage_type_id',
                    'range',
                    'long_range',
                    'distance_unit',
                    'is_improvised',
                    'is_simple',
                    'updated_at',
                ]
            );
        }
    }
}
