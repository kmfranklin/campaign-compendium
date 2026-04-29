<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the encounters table for saved encounter calculator results.
 *
 * This table is intentionally designed to grow into the full encounter
 * management feature (initiative tracking, round notes, etc.) rather than
 * being a throwaway calculator artifact. The JSON columns give us the
 * flexibility to extend the data shape without new migrations.
 *
 * PARTY
 * Stored as a JSON array of integer character levels, e.g. [3, 3, 5, 7].
 * Individual levels (rather than a count + average) allows accurate threshold
 * calculation for mixed-level parties.
 *
 * MONSTERS
 * JSON array of monster entries. Each entry carries enough data to render
 * the encounter without a live DB lookup — name, cr, and xp are snapshotted
 * at save time so the encounter remains accurate even if a creature is later
 * edited or deleted. The source field distinguishes SRD creatures, user-created
 * custom creatures, and manually-entered homebrew.
 *
 * Example entry:
 *   { "source": "srd", "creature_id": 42, "name": "Goblin",
 *     "cr": "1/4", "xp": 50, "quantity": 3 }
 *   { "source": "manual", "creature_id": null, "name": "Homebrew Boss",
 *     "cr": null, "xp": 10000, "quantity": 1 }
 *
 * CAMPAIGN FK
 * Nullable from day one so the encounter calculator can save standalone
 * encounters before the campaign management feature links them together.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();

            // Owner — only authenticated users can save encounters
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Optional campaign link — populated when the user attaches this
            // encounter to a campaign later; nulled if the campaign is deleted
            $table->foreignId('campaign_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // User-supplied label; null means the encounter is unnamed
            $table->string('name')->nullable();

            // Party: JSON array of integer character levels, e.g. [3, 3, 5, 7]
            $table->json('party');

            // Monsters: JSON array of snapshotted monster entries (see above)
            $table->json('monsters');

            // Calculated values stored at save time for quick display
            $table->unsignedInteger('total_xp');
            $table->unsignedInteger('adjusted_xp');
            $table->string('difficulty'); // 'easy' | 'medium' | 'hard' | 'deadly'

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
