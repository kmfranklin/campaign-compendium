<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Represents a broadcast message pushed to users by an admin.
 *
 * DELIVERY METHODS
 * ─────────────────
 * inbox   Fanned out to every user's notification inbox (bell icon) on
 *         activation. The fan-out is idempotent — sent_at is stamped after
 *         the first successful send and prevents duplicate inserts.
 *
 * banner  Shows as a dismissible page-wide alert on every page. Visibility
 *         is controlled in real time: toggling is_active or changing
 *         expires_at / show_at takes effect immediately without any fan-out.
 *
 * both    Combines inbox delivery AND banner display.
 *
 * SCOPES
 * ───────
 * scopeVisible()           Active + not expired. Used by the admin index to
 *                          show notifications regardless of delivery method.
 *
 * scopeVisibleForUser()    Visible banners only: active + not expired +
 *                          delivery includes banner + show_at has passed +
 *                          not dismissed by this user. Used by the banner
 *                          partial on every authenticated page load.
 */
class SystemNotification extends Model
{
    // ─── Type constants ──────────────────────────────────────────────────────
    public const TYPE_INFO    = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_DANGER  = 'danger';

    // ─── Delivery constants ──────────────────────────────────────────────────
    public const DELIVERY_INBOX  = 'inbox';
    public const DELIVERY_BANNER = 'banner';
    public const DELIVERY_BOTH   = 'both';

    // ─── Fillable ────────────────────────────────────────────────────────────
    protected $fillable = [
        'title',
        'message',
        'type',
        'delivery_method',
        'is_active',
        'expires_at',
        'show_at',
        'sent_at',
        'created_by',
    ];

    // ─── Casts ───────────────────────────────────────────────────────────────
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'expires_at' => 'datetime',
            'show_at'    => 'datetime',
            'sent_at'    => 'datetime',
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
     * Banner dismissal records — one per user who has dismissed this banner.
     * Only relevant for banner/both delivery notifications.
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
     * True if this notification's banner has already been dismissed by the given user.
     */
    public function isDismissedBy(User $user): bool
    {
        return $this->dismissals()->where('user_id', $user->id)->exists();
    }

    /**
     * True if inbox delivery has already been fanned out.
     */
    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Scope: notifications that are currently active and not yet expired.
     * Delivery-method-agnostic — used by the admin index to list all active
     * notifications regardless of how they're delivered.
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
     * Scope: banner notifications visible to the given user right now.
     *
     * Conditions stacked on top of scopeVisible():
     *   1. delivery_method is 'banner' or 'both'
     *   2. show_at is null (show immediately) OR show_at has already passed
     *   3. The user has not yet dismissed this banner
     *
     * This is what the system-notification-banners partial queries on
     * every authenticated page load.
     */
    public function scopeVisibleForUser(Builder $query, User $user): void
    {
        $query->visible()
              ->whereIn('delivery_method', [self::DELIVERY_BANNER, self::DELIVERY_BOTH])
              ->where(function (Builder $q) {
                  $q->whereNull('show_at')
                    ->orWhere('show_at', '<=', now());
              })
              ->whereDoesntHave('dismissals', function (Builder $q) use ($user) {
                  $q->where('user_id', $user->id);
              });
    }

    // ─── Inbox fan-out ───────────────────────────────────────────────────────

    /**
     * Fan this notification out to every current user's notification inbox.
     *
     * Creates one row in the `notifications` table per user, using the
     * existing TYPE_SYSTEM type. The data JSON column stores a snapshot of
     * the title, message, and type at send time — edits to this notification
     * after fan-out do not retroactively change what users see in their inbox.
     *
     * Idempotent: if sent_at is already set this method returns immediately.
     * Users created after the fan-out will not receive it automatically;
     * consider using the 'both' delivery method for welcome-style messages
     * so new users still see the banner.
     *
     * Rows are bulk-inserted in chunks of 500 to keep memory usage flat
     * regardless of user count.
     */
    public function fanOutToUsers(): void
    {
        if ($this->sent_at !== null) {
            return;
        }

        $data = [
            'system_notification_id' => $this->id,
            'title'                  => $this->title,
            'message'                => $this->message,
            'type'                   => $this->type,
        ];

        $now = now();

        User::orderBy('id')->chunk(500, function ($users) use ($data, $now) {
            $rows = $users->map(fn (User $user) => [
                'user_id'         => $user->id,
                'type'            => Notification::TYPE_SYSTEM,
                'notifiable_type' => static::class,
                'notifiable_id'   => $this->id,
                'data'            => json_encode($data),
                'read_at'         => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ])->all();

            DB::table('notifications')->insert($rows);
        });

        $this->update(['sent_at' => $now]);
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
     * Returns all valid delivery method values for validation and form selects.
     */
    public static function deliveries(): array
    {
        return [
            self::DELIVERY_INBOX  => 'Inbox',
            self::DELIVERY_BANNER => 'Banner',
            self::DELIVERY_BOTH   => 'Both',
        ];
    }

    /**
     * Pre-written message templates surfaced in the create/edit form.
     *
     * Each template sets a label, suggested type, delivery method, pre-filled
     * title, and pre-filled message. The admin can edit any field after
     * applying a template — these are starting points, not locked content.
     */
    public static function templates(): array
    {
        return [
            [
                'label'           => 'Scheduled Downtime',
                'type'            => self::TYPE_WARNING,
                'delivery_method' => self::DELIVERY_BOTH,
                'title'           => 'Scheduled Maintenance',
                'message'         => 'Campaign Compendium will be offline for scheduled maintenance on [DATE] from [START TIME] to [END TIME]. Please save your work before then.',
            ],
            [
                'label'           => 'Unplanned Outage',
                'type'            => self::TYPE_DANGER,
                'delivery_method' => self::DELIVERY_BOTH,
                'title'           => 'Service Disruption',
                'message'         => 'We are currently experiencing technical difficulties. Our team is working to resolve the issue. We apologize for the inconvenience.',
            ],
            [
                'label'           => 'Back Online',
                'type'            => self::TYPE_SUCCESS,
                'delivery_method' => self::DELIVERY_INBOX,
                'title'           => 'We\'re Back!',
                'message'         => 'Maintenance is complete and Campaign Compendium is fully operational again. Thank you for your patience.',
            ],
            [
                'label'           => 'New Feature',
                'type'            => self::TYPE_INFO,
                'delivery_method' => self::DELIVERY_INBOX,
                'title'           => 'New Feature Available',
                'message'         => 'We\'ve just launched [FEATURE NAME]! [Brief description of what it does and where to find it.]',
            ],
            [
                'label'           => 'Welcome',
                'type'            => self::TYPE_INFO,
                'delivery_method' => self::DELIVERY_BOTH,
                'title'           => 'Welcome to Campaign Compendium!',
                'message'         => 'Thanks for joining! Explore your dashboard to create campaigns, manage characters, and browse the item compendium. Happy adventuring!',
            ],
        ];
    }
}
