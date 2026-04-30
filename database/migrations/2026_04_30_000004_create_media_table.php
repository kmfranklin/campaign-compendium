<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Polymorphic media table — one upload system for the whole app.
 *
 * Currently wired to SessionLog (audio recordings), but the polymorphic
 * design means any future model (Npc portraits, Location maps, etc.) can
 * attach files without a new table or migration.
 *
 * Files are stored on the 'private' disk (storage/app/private) and served
 * through MediaController, which enforces campaign-membership authorization
 * before streaming the file to the browser.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // Polymorphic columns: mediable_type + mediable_id
            // e.g. mediable_type = "App\Models\SessionLog", mediable_id = 12
            $table->morphs('mediable');

            // The original filename as uploaded by the user
            $table->string('filename');

            // The path on disk (relative to the storage disk root)
            $table->string('path');

            // MIME type, e.g. "audio/mpeg", "image/png"
            $table->string('mime_type')->nullable();

            // File size in bytes — useful for display ("42.3 MB")
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
