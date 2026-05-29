<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReferenceDataSyncCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_data_sync_can_be_rerun_safely(): void
    {
        $this->artisan('reference-data:sync')->assertExitCode(0);
        $firstCounts = $this->referenceCounts();

        $this->artisan('reference-data:sync')->assertExitCode(0);
        $secondCounts = $this->referenceCounts();

        $this->assertSame($firstCounts, $secondCounts);
        $this->assertGreaterThan(0, $secondCounts['items']);
        $this->assertGreaterThan(0, $secondCounts['spells']);
        $this->assertGreaterThan(0, $secondCounts['creatures']);
        $this->assertGreaterThan(0, $secondCounts['rules']);
        $this->assertGreaterThan(0, $secondCounts['conditions']);
    }

    private function referenceCounts(): array
    {
        return [
            'items' => DB::table('items')->where('is_srd', true)->count(),
            'spells' => DB::table('spells')->where('is_srd', true)->count(),
            'creatures' => DB::table('creatures')->where('is_srd', true)->count(),
            'rules' => DB::table('rules')->count(),
            'conditions' => DB::table('conditions')->count(),
        ];
    }
}
