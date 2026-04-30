<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds DM-only notes to quests.
 *
 * 'description' is the public-facing quest summary (what players know).
 * 'notes' is private DM context: hidden clues, planned twists, session prep.
 * Keeping them separate prepares us cleanly for the DM vs. Player view
 * distinction in Phase 3 without needing a schema change later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
