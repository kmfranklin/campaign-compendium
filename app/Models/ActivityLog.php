<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * Disable updated_at entirely.
     *
     * Laravel's Model class defines two timestamp constants: CREATED_AT and
     * UPDATED_AT. Setting UPDATED_AT to null tells Eloquent not to look for
     * or populate that column. Since activity logs are immutable, we only
     * ever want a created_at — there is no "updated" state for a log entry.
     */
    public const UPDATED_AT = null;

    /**
     * Event type constants.
     *
     * Defining these as constants (rather than bare strings scattered across
     * the codebase) means: (a) typos cause a PHP error instead of a silent
     * wrong value, and (b) you can search for usages of a specific event type
     * with "find all references" in your IDE.
     */
    public const EVENT_USER_UPDATED             = 'user.updated';
    public const EVENT_USER_SUSPENDED           = 'user.suspended';
    public const EVENT_USER_UNSUSPENDED         = 'user.unsuspended';
    public const EVENT_IMPERSONATION_STARTED    = 'impersonation.started';
    public const EVENT_IMPERSONATION_ENDED      = 'impersonation.ended';
    public const EVENT_NOTIFICATION_CREATED     = 'notification.created';
    public const EVENT_NOTIFICATION_UPDATED     = 'notification.updated';
    public const EVENT_NOTIFICATION_DELETED     = 'notification.deleted';

    protected $fillable = [
        'admin_id',
        'target_user_id',
        'event',
        'description',
        'metadata',
        'ip_address',
    ];

    /**
     * Cast metadata from a JSON string in the database to a PHP array
     * automatically, so callers never need to call json_decode() themselves.
     */
    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The super admin who performed the action.
     * Nullable because the admin could be deleted after the fact.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * The user who was affected by the action, if any.
     * Nullable because some events have no target (and targets can be deleted).
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    // -------------------------------------------------------------------------
    // Static helper
    // -------------------------------------------------------------------------

    /**
     * Record an admin action to the log.
     *
     * Calling this as a static method keeps the call sites clean:
     *
     *   ActivityLog::record(ActivityLog::EVENT_USER_SUSPENDED, 'Super Admin suspended Kevin', $kevin);
     *
     * We read the current authenticated user and request IP here rather than
     * requiring callers to pass them in — every log entry should capture those
     * values automatically without callers having to remember.
     *
     * @param  string     $event       One of the EVENT_* constants.
     * @param  string     $description Human-readable sentence for the log view.
     * @param  User|null  $targetUser  The user who was affected, if applicable.
     * @param  array      $metadata    Optional structured context (e.g. changed fields).
     */
    public static function record(
        string $event,
        string $description,
        ?User $targetUser = null,
        array $metadata = []
    ): static {
        return static::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $targetUser?->id,
            'event'          => $event,
            'description'    => $description,
            'metadata'       => empty($metadata) ? null : $metadata,
            'ip_address'     => request()->ip(),
        ]);
    }
}
