<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the spells table.
 *
 * Stores SRD spells and any custom spells created by users. The is_srd flag
 * and user_id column distinguish SRD content (is_srd = true, user_id = null)
 * from user-created content (is_srd = false, user_id = owner). This mirrors
 * the pattern used by the items table.
 *
 * The base_spell_id self-referential FK supports cloning: when a user clones
 * an SRD spell to customise it, base_spell_id points back to the original.
 * Custom spells created from scratch leave base_spell_id null.
 *
 * SPELL SCHOOL
 * spell_school_id is a standard integer FK to spell_schools.id. The seeder
 * resolves the SRD slug to an ID before inserting, matching how item_category_id
 * and item_rarity_id are handled in ItemsTableSeeder.
 *
 * CLASSES & DAMAGE TYPES
 * Stored as JSON arrays (e.g. ['srd_wizard', 'srd_druid']). A proper pivot
 * table would require a full classes table, which will come in Phase 5. For
 * now JSON is accurate and easy to filter on for the lookup page.
 *
 * LEVEL
 * 0 = cantrip. 1–9 = levelled spell slot.
 *
 * CASTING TIME
 * SRD values: action, bonus-action, reaction, 1minute, 10minutes, 1hour,
 * 8hours, 12hours, 24hours.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spells', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique(); // SRD pk (e.g. 'srd_acid-arrow'); null for custom spells
            $table->string('name');
            $table->unsignedTinyInteger('level');         // 0 = cantrip, 1–9 = spell level

            // School — integer FK matching the rest of the codebase's convention
            $table->foreignId('spell_school_id')
                  ->nullable()
                  ->constrained('spell_schools')
                  ->restrictOnDelete();

            $table->string('casting_time')->nullable();   // 'action', 'bonus-action', '1minute', etc.
            $table->string('duration')->nullable();
            $table->string('range_text')->nullable();     // e.g. '90 feet', 'Self', 'Touch'

            // Components
            $table->boolean('verbal')->default(false);
            $table->boolean('somatic')->default(false);
            $table->boolean('material')->default(false);
            $table->boolean('material_consumed')->default(false);
            $table->string('material_specified')->nullable(); // e.g. "a pinch of salt and an adder's stomach"

            $table->boolean('concentration')->default(false);
            $table->boolean('ritual')->default(false);

            $table->text('description')->nullable();
            $table->text('higher_level')->nullable();     // Upcast description, if any

            // JSON arrays — e.g. ['srd_wizard', 'srd_druid'] / ['acid', 'fire']
            $table->json('classes')->nullable();
            $table->json('damage_types')->nullable();

            $table->string('saving_throw_ability')->nullable(); // 'dex', 'con', etc.

            // Ownership & SRD flag — mirrors the items table pattern exactly
            $table->boolean('is_srd')->default(false);
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            // Cloning — points to the spell this was derived from (null if original)
            $table->foreignId('base_spell_id')
                  ->nullable()
                  ->constrained('spells')
                  ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spells');
    }
};
