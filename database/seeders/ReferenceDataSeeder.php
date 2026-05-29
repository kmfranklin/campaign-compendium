<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ItemCategoriesTableSeeder::class,
            ItemRaritiesTableSeeder::class,
            DamageTypesTableSeeder::class,
            ItemsTableSeeder::class,
            WeaponsTableSeeder::class,
            ArmorsTableSeeder::class,
            SpellSchoolsTableSeeder::class,
            SpellsTableSeeder::class,
            CreatureTypesTableSeeder::class,
            CreaturesTableSeeder::class,
            RuleSetsTableSeeder::class,
            RulesTableSeeder::class,
            ConditionsTableSeeder::class,
        ]);
    }
}
