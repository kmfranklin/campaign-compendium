<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the spell_schools reference table.
 *
 * Spell schools are the eight categories of magic in D&D 5e (Abjuration,
 * Conjuration, Divination, Enchantment, Evocation, Illusion, Necromancy,
 * Transmutation). Each spell belongs to exactly one school.
 *
 * The slug column stores the pk from the SRD JSON (e.g. 'abjuration') and
 * is used as the join key from the spells table, mirroring the pattern used
 * by item_categories and item_rarities.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spell_schools', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();   // SRD pk, e.g. 'abjuration'
            $table->string('name');             // Display name, e.g. 'Abjuration'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_schools');
    }
};
