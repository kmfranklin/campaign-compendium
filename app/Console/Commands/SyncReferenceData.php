<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncReferenceData extends Command
{
    protected $signature = 'reference-data:sync';

    protected $description = 'Upsert shared SRD/reference data for items, spells, monsters, rules, and conditions';

    public function handle(): int
    {
        $this->info('Syncing shared reference data...');

        $exitCode = Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\ReferenceDataSeeder',
            '--force' => true,
        ], $this->output);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $this->newLine();
        $this->table(['Dataset', 'Rows'], [
            ['Item categories', DB::table('item_categories')->count()],
            ['Item rarities', DB::table('item_rarities')->count()],
            ['Damage types', DB::table('damage_types')->count()],
            ['Items', DB::table('items')->where('is_srd', true)->count()],
            ['Weapons', DB::table('weapons')->count()],
            ['Armors', DB::table('armors')->count()],
            ['Spell schools', DB::table('spell_schools')->count()],
            ['Spells', DB::table('spells')->where('is_srd', true)->count()],
            ['Creature types', DB::table('creature_types')->count()],
            ['Creatures', DB::table('creatures')->where('is_srd', true)->count()],
            ['Rule sets', DB::table('rule_sets')->count()],
            ['Rules', DB::table('rules')->count()],
            ['Conditions', DB::table('conditions')->count()],
        ]);

        $this->info('Reference data sync complete.');

        return self::SUCCESS;
    }
}
