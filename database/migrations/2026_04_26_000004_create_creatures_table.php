<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the creatures table for the SRD bestiary and user-created monsters.
 *
 * Stores SRD creatures and any custom creatures created by users. The is_srd
 * flag and user_id column distinguish SRD content from user-created content,
 * mirroring the pattern used by the items table.
 *
 * The base_creature_id self-referential FK supports cloning: when a user clones
 * an SRD creature as a starting point, base_creature_id points back to the
 * original. Custom creatures created from scratch leave it null.
 *
 * CREATURE TYPE
 * creature_type_id is a standard integer FK to creature_types.id. The seeder
 * resolves the SRD slug to an ID before inserting, matching how other reference
 * table IDs are handled throughout the codebase.
 *
 * ACTIONS & TRAITS
 * Stored as JSON arrays of {name, desc, action_type?, legendary_cost?} objects.
 * This avoids two extra tables for data that is displayed but never individually
 * queried or filtered. Users editing a custom creature will be able to add/remove
 * entries from these arrays through the UI.
 *
 * SPEED & SENSES
 * Each movement type and sense is a nullable unsigned smallInteger (feet).
 * Null means the creature simply doesn't have that speed or sense.
 *
 * SAVING THROWS & SKILL BONUSES
 * JSON objects keyed by ability/skill name containing only proficient entries,
 * e.g. {"constitution": 6, "wisdom": 6}. Non-proficient saves/skills are omitted.
 *
 * CHALLENGE RATING
 * Stored as a string to preserve fractional values ('1/8', '1/4', '1/2', '0').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creatures', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable()->unique(); // SRD pk (e.g. 'srd_aboleth'); null for custom creatures
            $table->string('name');

            // Type — integer FK matching the rest of the codebase's convention
            $table->foreignId('creature_type_id')
                  ->nullable()
                  ->constrained('creature_types')
                  ->restrictOnDelete();

            $table->string('size')->nullable();           // tiny, small, medium, large, huge, gargantuan
            $table->string('alignment')->nullable();

            // Defences
            $table->unsignedSmallInteger('armor_class')->nullable();
            $table->string('armor_detail')->nullable();   // e.g. 'natural armor', 'chain mail'
            $table->unsignedSmallInteger('hit_points')->nullable();
            $table->string('hit_dice')->nullable();       // e.g. '18d10+36'

            // CR as string to preserve '1/8', '1/4', '1/2'
            $table->string('challenge_rating')->nullable();

            // Ability scores
            $table->unsignedTinyInteger('ability_score_strength')->nullable();
            $table->unsignedTinyInteger('ability_score_dexterity')->nullable();
            $table->unsignedTinyInteger('ability_score_constitution')->nullable();
            $table->unsignedTinyInteger('ability_score_intelligence')->nullable();
            $table->unsignedTinyInteger('ability_score_wisdom')->nullable();
            $table->unsignedTinyInteger('ability_score_charisma')->nullable();

            // Proficient saving throws — JSON object e.g. {"constitution": 6, "wisdom": 6}
            $table->json('saving_throws')->nullable();

            // Proficient skill bonuses — JSON object e.g. {"history": 12, "perception": 10}
            $table->json('skill_bonuses')->nullable();

            // Damage modifiers — JSON arrays of damage type strings
            $table->json('damage_immunities')->nullable();
            $table->json('damage_resistances')->nullable();
            $table->json('damage_vulnerabilities')->nullable();
            $table->json('condition_immunities')->nullable();

            $table->boolean('nonmagical_attack_immunity')->default(false);
            $table->boolean('nonmagical_attack_resistance')->default(false);

            // Speeds in feet — null means creature lacks that movement type
            $table->unsignedSmallInteger('speed_walk')->nullable();
            $table->unsignedSmallInteger('speed_fly')->nullable();
            $table->unsignedSmallInteger('speed_swim')->nullable();
            $table->unsignedSmallInteger('speed_climb')->nullable();
            $table->unsignedSmallInteger('speed_burrow')->nullable();
            $table->boolean('speed_hover')->default(false);

            // Senses in feet — null means creature lacks that sense
            $table->unsignedSmallInteger('sense_darkvision')->nullable();
            $table->unsignedSmallInteger('sense_blindsight')->nullable();
            $table->unsignedSmallInteger('sense_tremorsense')->nullable();
            $table->unsignedSmallInteger('sense_truesight')->nullable();
            $table->unsignedSmallInteger('sense_telepathy')->nullable();
            $table->unsignedSmallInteger('passive_perception')->nullable();

            $table->string('languages_desc')->nullable();  // Free-text language list

            // JSON arrays of {name, desc, ...} objects — displayed but not individually queried
            $table->json('traits')->nullable();
            $table->json('actions')->nullable();           // includes legendary actions and reactions

            // Ownership & SRD flag — mirrors the items table pattern exactly
            $table->boolean('is_srd')->default(false);
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            // Cloning — points to the creature this was derived from (null if original)
            $table->foreignId('base_creature_id')
                  ->nullable()
                  ->constrained('creatures')
                  ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creatures');
    }
};
