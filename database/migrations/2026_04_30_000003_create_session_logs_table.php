<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // A human-readable title for the session, e.g. "Session 4: The Sunken Temple"
            $table->string('title');

            // The actual date the session was played (not a timestamp — a plain date)
            $table->date('session_date');

            // Freeform DM notes: what happened, loose ends, memorable moments
            $table->text('summary')->nullable();

            $table->timestamps();

            // Most common query: all sessions for a campaign, newest first
            $table->index(['campaign_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_logs');
    }
};
