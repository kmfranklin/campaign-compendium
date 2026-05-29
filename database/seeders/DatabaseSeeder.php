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
        $this->call(ReferenceDataSeeder::class);

        if (! app()->environment('local')) {
            return;
        }

        // --- Dev/test accounts ---
        $devUser = User::firstOrCreate([
            'email' => 'kevin@example.com',
        ], [
            'name' => 'Kevin',
            'password' => bcrypt('password'),
        ]);

        $supportUsers = collect([
            User::firstOrCreate(
                ['email' => 'ally1@example.com'],
                ['name' => 'Campaign Ally 1', 'password' => bcrypt('password')]
            ),
            User::firstOrCreate(
                ['email' => 'ally2@example.com'],
                ['name' => 'Campaign Ally 2', 'password' => bcrypt('password')]
            ),
        ]);

        collect([$devUser, ...$supportUsers])->each(function (User $user) {
            $missingNpcCount = max(0, 50 - $user->npcs()->count());

            if ($missingNpcCount > 0) {
                Npc::factory()->count($missingNpcCount)->create([
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
