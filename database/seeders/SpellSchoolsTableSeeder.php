<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpellSchoolsTableSeeder extends Seeder
{
    public function run(): void
    {
        $schools = json_decode(
            file_get_contents(base_path('database/data/SpellSchool.json')),
            true
        );

        $rows = collect($schools)->map(fn ($entry) => [
            'slug'        => $entry['pk'],
            'name'        => $entry['fields']['name'],
            'description' => $entry['fields']['desc'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ])->all();

        DB::table('spell_schools')->insert($rows);
    }
}
