<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArmorsTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/Armor.json');
        $json = json_decode(file_get_contents($path), true);
        $itemIds = DB::table('items')->pluck('id', 'item_key');

        $armors = collect($json)->map(function ($entry) use ($itemIds) {
            $fields = $entry['fields'];

            return [
                'item_id' => $itemIds[$entry['pk']] ?? null,

                'base_ac' => $fields['ac_base'] ?? 0,
                'adds_dex_mod' => $fields['ac_add_dexmod'] ?? true,
                'dex_mod_cap' => $fields['ac_cap_dexmod'] ?? null,
                'imposes_stealth_disadvantage' => $fields['grants_stealth_disadvantage'] ?? false,
                'strength_requirement' => $fields['strength_score_required'] ?? null,

                'created_at' => now(),
                'updated_at' => now(),
            ];
        })
        ->filter(fn ($armor) => $armor['item_id'] !== null)
        ->all();

        foreach (array_chunk($armors, 100) as $chunk) {
            DB::table('armors')->upsert(
                $chunk,
                ['item_id'],
                [
                    'base_ac',
                    'adds_dex_mod',
                    'dex_mod_cap',
                    'imposes_stealth_disadvantage',
                    'strength_requirement',
                    'updated_at',
                ]
            );
        }
    }
}
