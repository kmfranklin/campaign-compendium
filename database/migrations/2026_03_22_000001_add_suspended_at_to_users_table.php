<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a nullable suspended_at timestamp to the users table.
     *
     * Using a timestamp instead of a boolean gives us three benefits:
     *   1. It records *when* the suspension happened for the audit trail.
     *   2. NULL = active, NOT NULL = suspended — no ambiguous false/0 states.
     *   3. We can query "suspended since X" without extra columns.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migration by dropping the column.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('suspended_at');
        });
    }
};
