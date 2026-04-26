<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a broadcast message pushed to all authenticated users.
 *
 * An "active" notification is one where is_active = true AND either expires_at
 * is null or expires_at is in the future. The scopeVisible() scope encodes
 * both conditions so callers don't have to repeat them.
 *
 * The scopeVisibleForUser() scope builds on scopeVisible() and additionally
 * excludes notifications that the given user has already dismissed, which is
 * the query used by the banner partial on every page load.
 */
class SystemNotification extends Model
{
    // ─── Type constants ──────────────────────────────────────────────────────
    // These map to the banner colour and icon used in the UI.
    public const TYPE_INFO    = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_DANGER  = 'danger';

    // ─── Fillable ────────────────────────────────────────────────────────────
    protected $fillable = [
        'title',
        'message',
        'type',
        'is_active',
        'expires_at',
        'created_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * The admin who created this notification.
     * May be null if that account was later deleted (nullOnDelete FK).
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * All dismissal records for this notification, one per user who dismissed it.
     */
    public function dismissals(): HasMany
    {
        return $this->hasMany(SystemNotificationDismissal::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * True if expires_at is set and is in the past.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * True if this notification has already been dismissed by the given user.
     */
    public function isDismissedBy(User $user): bool
    {
        return $this->dismissals()->where('user_id', $user->id)->exists();
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Scope: notifications that are currently active and not yet expired.
     * This is the base "should this show at all?" filter.
     */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)
              ->where(function (Builder $q) {
                  $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
              });
    }

    /**
     * Scope: visible notifications that the given user has not yet dismissed.
     * This is what the banner partial queries on every authenticated page load.
     */
    public function scopeVisibleForUser(Builder $query, User $user): void
    {
        $query->visible()
              ->whereDoesntHave('dismissals', function (Builder $q) use ($user) {
                  $q->where('user_id', $user->id);
              });
    }

    // ─── Helpers for admin UI ────────────────────────────────────────────────

    /**
     * Returns all valid type values for use in validation rules and form selects.
     */
    public static function types(): array
    {
        return [
            self::TYPE_INFO    => 'Info',
            self::TYPE_WARNING => 'Warning',
            self::TYPE_SUCCESS => 'Success',
            self::TYPE_DANGER  => 'Danger',
        ];
    }

    /**
     * Pre-written message templates surfaced in the create/edit form.
     *
     * Each template has a label (shown in the selector), a suggested type,
     * a pre-filled title, and a pre-filled message body. The admin can edit
     * any field after applying a template — these are starting points, not
     * locked content.
     *
     * Adding a new template here automatically makes it available in the UI
     * with no other changes required.
     */
    public static function templates(): array
    {
        return [
            [
                'label'   => 'Scheduled Downtime',
                'type'    => self::TYPE_WARNING,
                'title'   => 'Scheduled Maintenance',
                'message' => 'Campaign Compendium will be offline for scheduled maintenance on [DATE] from [START TIME] to [END TIME]. Please save your work before then.',
            ],
            [
                'label'   => 'Unplanned Outage',
                'type'    => self::TYPE_DANGER,
                'title'   => 'Service Disruption',
                'message' => 'We are currently experiencing technical difficulties. Our team is working to resolve the issue. We apologize for the inconvenience.',
            ],
            [
                'label'   => 'Back Online',
                'type'    => self::TYPE_SUCCESS,
                'title'   => 'We\'re Back!',
                'message' => 'Maintenance is complete and Campaign Compendium is fully operational again. Thank you for your patience.',
            ],
            [
                'label'   => 'New Feature',
                'type'    => self::TYPE_INFO,
                'title'   => 'New Feature Available',
                'message' => 'We\'ve just launched [FEATURE NAME]! [Brief description of what it does and where to find it.]',
            ],
            [
                'label'   => 'Welcome',
                'type'    => self::TYPE_INFO,
                'title'   => 'Welcome to Campaign Compendium!',
                'message' => 'Thanks for joining! Explore your dashboard to create campaigns, manage characters, and browse the item compendium. Happy adventuring!',
            ],
        ];
    }
}
