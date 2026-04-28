<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RulesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Build a slug → id lookup so we can resolve the foreign key
        // without firing a query inside the loop.
        $ruleSetIds = DB::table('rule_sets')->pluck('id', 'slug');

        $data = json_decode(
            file_get_contents(database_path('data/Rule.json')),
            associative: true
        );

        $rows = [];

        foreach ($data as $item) {
            $ruleSetSlug = $item['fields']['ruleset'];

            if (! isset($ruleSetIds[$ruleSetSlug])) {
                // Skip any rule that references an unknown ruleset rather than
                // crashing the whole seeder — makes the seeder resilient to
                // data inconsistencies in the source JSON.
                $this->command->warn("Skipping rule '{$item['pk']}': unknown ruleset '{$ruleSetSlug}'");
                continue;
            }

            $rows[] = [
                'slug'        => $item['pk'],
                'name'        => $item['fields']['name'],
                'rule_set_id' => $ruleSetIds[$ruleSetSlug],
                'body'        => $item['fields']['desc'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('rules')->insert($rows);

        $this->command->info('Seeded ' . count($rows) . ' rules.');
    }
}
