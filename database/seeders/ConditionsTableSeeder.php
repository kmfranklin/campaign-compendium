<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('data/Condition.json')),
            associative: true
        );

        $rows = array_map(fn ($item) => [
            'slug'       => $item['pk'],
            'name'       => $item['fields']['name'],
            'body'       => $item['fields']['desc'],
            'created_at' => now(),
            'updated_at' => now(),
        ], $data);

        DB::table('conditions')->insert($rows);

        $this->command->info('Seeded ' . count($rows) . ' conditions.');
    }
}
