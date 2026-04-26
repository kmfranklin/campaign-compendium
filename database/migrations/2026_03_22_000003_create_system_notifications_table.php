<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * System Notifications table.
 *
 * Stores broadcast messages that admins can push to all authenticated users.
 * Each notification has a type (info/warning/success/danger) that controls
 * the colour of the banner rendered in the app layout.
 *
 * is_active lets admins pause/resume a notification without deleting it.
 * expires_at (nullable) automatically hides the notification after a given
 * date — useful for maintenance windows and time-sensitive announcements.
 * created_by uses nullOnDelete() so log entries survive admin account deletion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('message');

            // Controls banner colour: info (blue), warning (amber),
            // success (green), danger (red).
            $table->enum('type', ['info', 'warning', 'success', 'danger'])
                  ->default('info');

            // Soft on/off toggle — keeps history, avoids destructive deletes
            // when an admin just wants to temporarily hide a notification.
            $table->boolean('is_active')->default(true);

            // Optional expiry. NULL means the notification never expires on
            // its own; a non-null value hides it automatically after that
            // date/time even if is_active is still true.
            $table->timestamp('expires_at')->nullable();

            // Who created this notification. Nullable + nullOnDelete so the
            // notification survives if the admin account is later deleted.
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
