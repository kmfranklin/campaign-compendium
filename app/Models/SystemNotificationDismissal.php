<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Records that a specific user dismissed a specific system notification.
 *
 * This table is append-only — rows are never updated after creation.
 * Setting UPDATED_AT = null tells Eloquent not to touch an updated_at column
 * (which doesn't exist on this table).
 *
 * The unique constraint on (system_notification_id, user_id) in the migration
 * prevents duplicates at the database level. In the controller we use
 * firstOrCreate() to avoid a race condition on the application level.
 */
class SystemNotificationDismissal extends Model
{
    // The timestamp column is called dismissed_at, not the Eloquent default created_at.
    // CREATED_AT tells Eloquent which column to auto-populate on insert.
    // UPDATED_AT = null because this table is append-only (no updates ever happen).
    public const CREATED_AT = 'dismissed_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'system_notification_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'dismissed_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function notification(): BelongsTo
    {
        return $this->belongsTo(SystemNotification::class, 'system_notification_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
