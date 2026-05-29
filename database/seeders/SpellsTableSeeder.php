<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpellsTableSeeder extends Seeder
{
    public function run(): void
    {
        $spells = json_decode(
            file_get_contents(base_path('database/data/Spell.json')),
            true
        );

        // Pre-load school IDs keyed by slug so we do one query per school
        // (8 total) rather than one query per spell (319 total).
        // This is the same technique ItemsTableSeeder uses for categories/rarities.
        $schoolIds = DB::table('spell_schools')
            ->pluck('id', 'slug'); // ['abjuration' => 1, 'conjuration' => 2, ...]

        $rows = collect($spells)->map(function ($entry) use ($schoolIds) {
            $f = $entry['fields'];

            return [
                'slug'                 => $entry['pk'],
                'name'                 => $f['name'],
                'level'                => $f['level'],
                'spell_school_id'      => $schoolIds[$f['school']] ?? null,
                'casting_time'         => $f['casting_time'] ?? null,
                'duration'             => $f['duration'] ?? null,
                'range_text'           => $f['range_text'] ?? null,
                'verbal'               => $f['verbal'] ?? false,
                'somatic'              => $f['somatic'] ?? false,
                'material'             => $f['material'] ?? false,
                'material_consumed'    => $f['material_consumed'] ?? false,
                'material_specified'   => $f['material_specified'] ?? null,
                'concentration'        => $f['concentration'] ?? false,
                'ritual'               => $f['ritual'] ?? false,
                'description'          => $f['desc'] ?? null,
                'higher_level'         => $f['higher_level'] ?? null,
                'classes'              => json_encode($f['classes'] ?? []),
                'damage_types'         => json_encode($f['damage_types'] ?? []),
                'saving_throw_ability' => ($f['saving_throw_ability'] ?? '') !== ''
                                            ? $f['saving_throw_ability']
                                            : null,
                'is_srd'               => true,
                'user_id'              => null,
                'base_spell_id'        => null,
                'deleted_at'           => null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        })->all();

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('spells')->upsert(
                $chunk,
                ['slug'],
                [
                    'name',
                    'level',
                    'spell_school_id',
                    'casting_time',
                    'duration',
                    'range_text',
                    'verbal',
                    'somatic',
                    'material',
                    'material_consumed',
                    'material_specified',
                    'concentration',
                    'ritual',
                    'description',
                    'higher_level',
                    'classes',
                    'damage_types',
                    'saving_throw_ability',
                    'is_srd',
                    'user_id',
                    'base_spell_id',
                    'deleted_at',
                    'updated_at',
                ]
            );
        }
    }
}
