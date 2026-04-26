<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreaturesTableSeeder extends Seeder
{
    public function run(): void
    {
        $creatures = json_decode(
            file_get_contents(base_path('database/data/Creature.json')),
            true
        );

        // Pre-load type IDs keyed by slug — same technique as SpellsTableSeeder.
        $typeIds = DB::table('creature_types')->pluck('id', 'slug');

        // Pre-load and group actions and traits by their parent creature slug.
        // We do this once up front so we're not re-reading the files 325 times.
        $actions = collect(json_decode(
            file_get_contents(base_path('database/data/CreatureAction.json')),
            true
        ))->groupBy(fn ($entry) => $entry['fields']['parent']);

        $traits = collect(json_decode(
            file_get_contents(base_path('database/data/CreatureTrait.json')),
            true
        ))->groupBy(fn ($entry) => $entry['fields']['parent']);

        $rows = collect($creatures)->map(function ($entry) use ($typeIds, $actions, $traits) {
            $f   = $entry['fields'];
            $slug = $entry['pk'];

            // Build saving throws JSON — only include abilities with a non-null bonus.
            $savingThrows = array_filter([
                'strength'     => $f['saving_throw_strength']     ?? null,
                'dexterity'    => $f['saving_throw_dexterity']    ?? null,
                'constitution' => $f['saving_throw_constitution']  ?? null,
                'intelligence' => $f['saving_throw_intelligence']  ?? null,
                'wisdom'       => $f['saving_throw_wisdom']        ?? null,
                'charisma'     => $f['saving_throw_charisma']      ?? null,
            ], fn ($v) => $v !== null);

            // Build skill bonuses JSON — only include skills with a non-null bonus.
            $skillBonuses = array_filter([
                'acrobatics'     => $f['skill_bonus_acrobatics']     ?? null,
                'animal_handling'=> $f['skill_bonus_animal_handling'] ?? null,
                'arcana'         => $f['skill_bonus_arcana']          ?? null,
                'athletics'      => $f['skill_bonus_athletics']       ?? null,
                'deception'      => $f['skill_bonus_deception']       ?? null,
                'history'        => $f['skill_bonus_history']         ?? null,
                'insight'        => $f['skill_bonus_insight']         ?? null,
                'intimidation'   => $f['skill_bonus_intimidation']    ?? null,
                'investigation'  => $f['skill_bonus_investigation']   ?? null,
                'medicine'       => $f['skill_bonus_medicine']        ?? null,
                'nature'         => $f['skill_bonus_nature']          ?? null,
                'perception'     => $f['skill_bonus_perception']      ?? null,
                'performance'    => $f['skill_bonus_performance']     ?? null,
                'persuasion'     => $f['skill_bonus_persuasion']      ?? null,
                'religion'       => $f['skill_bonus_religion']        ?? null,
                'sleight_of_hand'=> $f['skill_bonus_sleight_of_hand'] ?? null,
                'stealth'        => $f['skill_bonus_stealth']         ?? null,
                'survival'       => $f['skill_bonus_survival']        ?? null,
            ], fn ($v) => $v !== null);

            // Convert decimal CR string to a human-readable value.
            // The SRD stores '0.12500', '0.25000', '0.50000' for fractional CRs
            // and '10.000', '1.000' etc. for whole numbers.
            $cr = $f['challenge_rating_decimal'] ?? '0';
            $crDisplay = match (true) {
                str_starts_with($cr, '0.125') => '1/8',
                str_starts_with($cr, '0.25')  => '1/4',
                str_starts_with($cr, '0.5')   => '1/2',
                default                        => (string) (int) round((float) $cr),
            };

            // Map actions for this creature into a compact array of objects.
            $creatureActions = ($actions[$slug] ?? collect())->sortBy(
                fn ($a) => $a['fields']['order'] ?? 0
            )->map(fn ($a) => array_filter([
                'name'           => $a['fields']['name'],
                'desc'           => $a['fields']['desc'],
                'action_type'    => $a['fields']['action_type'] ?? null,
                'legendary_cost' => $a['fields']['legendary_cost'] ?? null,
            ], fn ($v) => $v !== null))->values()->all();

            // Map traits for this creature into a compact array of objects.
            $creatureTraits = ($traits[$slug] ?? collect())->map(
                fn ($t) => [
                    'name' => $t['fields']['name'],
                    'desc' => $t['fields']['desc'],
                ]
            )->values()->all();

            return [
                'slug'                         => $slug,
                'name'                         => $f['name'],
                'creature_type_id'             => $typeIds[$f['type']] ?? null,
                'size'                         => $f['size'] ?? null,
                'alignment'                    => $f['alignment'] ?? null,
                'armor_class'                  => $f['armor_class'] ?? null,
                'armor_detail'                 => $f['armor_detail'] ?? null,
                'hit_points'                   => $f['hit_points'] ?? null,
                'hit_dice'                     => $f['hit_dice'] ?? null,
                'challenge_rating'             => $crDisplay,
                'ability_score_strength'       => $f['ability_score_strength'] ?? null,
                'ability_score_dexterity'      => $f['ability_score_dexterity'] ?? null,
                'ability_score_constitution'   => $f['ability_score_constitution'] ?? null,
                'ability_score_intelligence'   => $f['ability_score_intelligence'] ?? null,
                'ability_score_wisdom'         => $f['ability_score_wisdom'] ?? null,
                'ability_score_charisma'       => $f['ability_score_charisma'] ?? null,
                'saving_throws'                => !empty($savingThrows) ? json_encode($savingThrows) : null,
                'skill_bonuses'                => !empty($skillBonuses) ? json_encode($skillBonuses) : null,
                'damage_immunities'            => json_encode($f['damage_immunities'] ?? []),
                'damage_resistances'           => json_encode($f['damage_resistances'] ?? []),
                'damage_vulnerabilities'       => json_encode($f['damage_vulnerabilities'] ?? []),
                'condition_immunities'         => json_encode($f['condition_immunities'] ?? []),
                'nonmagical_attack_immunity'   => $f['nonmagical_attack_immunity'] ?? false,
                'nonmagical_attack_resistance' => $f['nonmagical_attack_resistance'] ?? false,
                'speed_walk'                   => isset($f['walk'])   ? (int) $f['walk']   : null,
                'speed_fly'                    => isset($f['fly'])    ? (int) $f['fly']    : null,
                'speed_swim'                   => isset($f['swim'])   ? (int) $f['swim']   : null,
                'speed_climb'                  => isset($f['climb'])  ? (int) $f['climb']  : null,
                'speed_burrow'                 => isset($f['burrow']) ? (int) $f['burrow'] : null,
                'speed_hover'                  => $f['hover'] ?? false,
                'sense_darkvision'             => isset($f['darkvision_range'])  ? (int) $f['darkvision_range']  : null,
                'sense_blindsight'             => isset($f['blindsight_range'])  ? (int) $f['blindsight_range']  : null,
                'sense_tremorsense'            => isset($f['tremorsense_range']) ? (int) $f['tremorsense_range'] : null,
                'sense_truesight'              => isset($f['truesight_range'])   ? (int) $f['truesight_range']   : null,
                'sense_telepathy'              => isset($f['telepathy_range'])   ? (int) $f['telepathy_range']   : null,
                'passive_perception'           => $f['passive_perception'] ?? null,
                'languages_desc'               => $f['languages_desc'] ?? null,
                'traits'                       => !empty($creatureTraits)  ? json_encode($creatureTraits)  : null,
                'actions'                      => !empty($creatureActions) ? json_encode($creatureActions) : null,
                'is_srd'                       => true,
                'user_id'                      => null,
                'base_creature_id'             => null,
                'created_at'                   => now(),
                'updated_at'                   => now(),
            ];
        })->all();

        // Insert in chunks to keep memory usage flat — same approach as
        // fanOutToUsers() in the SystemNotification model.
        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('creatures')->insert($chunk);
        }
    }
}
