<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Npc;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Core reference data from Open5e ---
        $this->call([
            // Items
            ItemCategoriesTableSeeder::class,
            ItemRaritiesTableSeeder::class,
            DamageTypesTableSeeder::class,
            ItemsTableSeeder::class,
            WeaponsTableSeeder::class,
            ArmorsTableSeeder::class,
            // Spells (school reference table must come before spells)
            SpellSchoolsTableSeeder::class,
            SpellsTableSeeder::class,
            // Creatures (type reference table must come before creatures)
            CreatureTypesTableSeeder::class,
            CreaturesTableSeeder::class,
            // Rules & Conditions (rule sets must come before rules — FK dependency)
            RuleSetsTableSeeder::class,
            RulesTableSeeder::class,
            ConditionsTableSeeder::class,
        ]);

        // --- Dev/test accounts ---
        $devUser = User::factory()->create([
            'name' => 'Kevin',
            'email' => 'kevin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Give dev account 50 NPCs
        Npc::factory()->count(50)->create([
            'user_id' => $devUser->id,
        ]);

        // Create 2 more random users
        $otherUsers = User::factory()->count(2)->create();

        // Give each of them 50 NPCs
        foreach ($otherUsers as $user) {
            Npc::factory()->count(50)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
