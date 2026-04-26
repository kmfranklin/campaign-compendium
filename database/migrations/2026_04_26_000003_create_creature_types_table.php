<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the creature_types reference table.
 *
 * The 14 SRD creature types (Aberration, Beast, Celestial, etc.) used to
 * categorise monsters. Each creature belongs to exactly one type. Stored as
 * a separate table so the monster index can offer a type filter dropdown
 * without scanning the creatures table for distinct values.
 *
 * The slug column stores the SRD pk (e.g. 'aberration') and is used as the
 * join key from the creatures table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creature_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();   // SRD pk, e.g. 'aberration'
            $table->string('name');             // Display name, e.g. 'Aberration'
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_types');
    }
};
