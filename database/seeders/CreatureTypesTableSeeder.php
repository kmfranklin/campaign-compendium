<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreatureTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $types = json_decode(
            file_get_contents(base_path('database/data/CreatureType.json')),
            true
        );

        $rows = collect($types)->map(fn ($entry) => [
            'slug'        => $entry['pk'],
            'name'        => $entry['fields']['name'],
            'description' => $entry['fields']['desc'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ])->all();

        DB::table('creature_types')->insert($rows);
    }
}
