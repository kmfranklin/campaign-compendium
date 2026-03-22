<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Activity logs are intentionally append-only — there is no updated_at
     * column and no soft-deletes. A log entry should never be modified after
     * it is written; its only job is to be an immutable historical record.
     *
     * Both foreign keys use nullOnDelete() rather than cascadeOnDelete().
     * If an admin or target user is later deleted from the system, we want
     * to keep the log entry (the event still happened) but allow the FK to
     * become NULL rather than losing the entire row.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (the super admin)
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Who was affected, if applicable (e.g. the user being suspended)
            $table->foreignId('target_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Machine-readable event identifier, e.g. 'user.suspended'
            $table->string('event', 100);

            // Human-readable sentence describing exactly what happened,
            // e.g. "Super Admin suspended Magali Anderson"
            $table->text('description');

            // Optional structured data for richer context, e.g. which fields
            // were changed during an edit. Stored as JSON.
            $table->json('metadata')->nullable();

            // The IP address of the admin who performed the action.
            // Nullable because requests can theoretically arrive without one.
            $table->string('ip_address', 45)->nullable();

            // Append-only: only a created_at timestamp, no updated_at.
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
