<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CampaignInvite extends Model
{
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
}
