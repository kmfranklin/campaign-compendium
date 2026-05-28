<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CampaignInvite extends Model
{
    public const DEFAULT_EXPIRY_DAYS = 14;

    protected $fillable = [
        'campaign_id',
        'inviter_id',
        'invitee_id',
        'email',
        'token',
        'status',
        'message',
        'accepted_at',
        'declined_at',
        'expires_at',
    ];

    // Status constants
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_EXPIRED  = 'expired';
    public const STATUS_REVOKED  = 'revoked';

    protected $casts = [
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    // Relationships
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function canBeClaimedBy(User $user): bool
    {
        return strcasecmp($user->email, $this->email) === 0;
    }

    public function markExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    public function acceptFor(User $user): void
    {
        if (! $this->campaign->members()->where('user_id', $user->id)->exists()) {
            $this->campaign->members()->attach($user->id, [
                'role_id' => Role::PLAYER,
            ]);
        }

        $this->update([
            'invitee_id' => $user->id,
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        Notification::where('notifiable_type', self::class)
            ->where('notifiable_id', $this->id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);
    }

    public function declineFor(User $user): void
    {
        $this->update([
            'invitee_id' => $user->id,
            'status' => self::STATUS_DECLINED,
            'declined_at' => now(),
        ]);

        Notification::where('notifiable_type', self::class)
            ->where('notifiable_id', $this->id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);
    }
}
