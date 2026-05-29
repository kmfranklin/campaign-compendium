<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DamageTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/DamageType.json');
        $json = json_decode(file_get_contents($path), true);

        $types = collect($json)->map(function ($entry) {
            return [
                'slug' => $entry['pk'],                     // e.g. "slashing"
                'name' => $entry['fields']['name'],         // e.g. "Slashing"
                'description' => $entry['fields']['desc'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->all();

        DB::table('damage_types')->upsert(
            $types,
            ['slug'],
            ['name', 'description', 'updated_at']
        );
    }
}
