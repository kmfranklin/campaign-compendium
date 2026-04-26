<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * System Notification Dismissals table.
 *
 * Append-only pivot that records which user dismissed which notification.
 * Once a row exists here, that notification will never re-appear for that
 * user — even if it is deactivated and reactivated later.
 *
 * Both foreign keys use cascadeOnDelete() so orphaned rows are cleaned up
 * automatically when either the notification or the user is deleted.
 *
 * The unique constraint on (system_notification_id, user_id) prevents a user
 * from accumulating multiple dismissal records for the same notification.
 *
 * No updated_at column — this table is strictly append-only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notification_dismissals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('system_notification_id')
                  ->constrained('system_notifications')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // When the user dismissed this notification.
            $table->timestamp('dismissed_at')->useCurrent();

            // Prevent duplicate dismissal records for the same (notification, user) pair.
            $table->unique(['system_notification_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notification_dismissals');
    }
};
