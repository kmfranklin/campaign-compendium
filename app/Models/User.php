<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function npcs(): HasMany
    {
        return $this->hasMany(Npc::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_user')
                    ->withPivot('role_id')
                    ->withTimestamps();
    }

    public function campaignsAsDm(): BelongsToMany
    {
        return $this->campaigns()->wherePivot('role_id', Role::DM);
    }

    public function campaignsAsPlayer(): BelongsToMany
    {
        return $this->campaigns()->wherePivot('role_id', Role::PLAYER);
    }

    public function ownedCampaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'dm_id');
    }

    // Notification system
    public function sentInvites()
    {
        return $this->hasMany(CampaignInvite::class, 'inviter_id');
    }

    public function receivedInvites()
    {
        return $this->hasMany(CampaignInvite::class, 'invitee_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
