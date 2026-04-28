<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleSetsTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('data/RuleSet.json')),
            associative: true
        );

        $rows = array_map(fn ($item) => [
            'slug'       => $item['pk'],
            'name'       => $item['fields']['name'],
            'desc'       => $item['fields']['desc'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $data);

        DB::table('rule_sets')->insert($rows);

        $this->command->info('Seeded ' . count($rows) . ' rule sets.');
    }
}
