<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds three columns to the system_notifications table:
 *
 *   delivery_method  enum('inbox','banner','both')  default 'inbox'
 *       Controls how the notification reaches users:
 *         inbox  → fanned out to every user's notification inbox (bell icon)
 *         banner → shown as a dismissible page-wide alert
 *         both   → appears in the inbox AND shows as a banner
 *
 *   show_at  nullable timestamp
 *       For banner/both notifications: the earliest time the banner will
 *       appear. Null means "show immediately when active." Useful for
 *       scheduling a "site goes down in 5 minutes" banner to appear
 *       automatically before a maintenance window.
 *
 *   sent_at  nullable timestamp
 *       Stamped when the inbox fan-out completes. A null value means the
 *       notification has not yet been sent to inboxes. The fanOutToUsers()
 *       method checks this before inserting to prevent duplicate sends.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_notifications', function (Blueprint $table) {
            $table->enum('delivery_method', ['inbox', 'banner', 'both'])
                  ->default('inbox')
                  ->after('type');

            $table->timestamp('show_at')
                  ->nullable()
                  ->after('expires_at');

            $table->timestamp('sent_at')
                  ->nullable()
                  ->after('show_at');
        });
    }

    public function down(): void
    {
        Schema::table('system_notifications', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'show_at', 'sent_at']);
        });
    }
};
