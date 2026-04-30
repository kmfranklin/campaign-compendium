<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class Campaign extends Model
{
    protected $fillable = ['name', 'description', 'dm_id'];

    // Owner DM (explicit FK)
    public function dm()
    {
        return $this->belongsTo(User::class, 'dm_id');
    }

    // All members (DM + players) via pivot
    public function members()
    {
        return $this->belongsToMany(User::class, 'campaign_user')
                    ->withPivot('role_id')
                    ->withTimestamps();
    }

    // Filtered members
    public function players()
    {
        return $this->members()->wherePivot('role_id', Role::PLAYER);
    }

    public function dms()
    {
        return $this->members()->wherePivot('role_id', Role::DM);
    }

    // Quests in this campaign
    public function quests()
    {
        return $this->hasMany(Quest::class);
    }

    // Campaign‑wide NPCs
    public function npcs()
    {
        return $this->hasMany(Npc::class);
    }

    // Session logs, newest first
    public function sessionLogs()
    {
        return $this->hasMany(SessionLog::class)->orderByDesc('session_date');
    }

    // Invite notifications
    public function invites()
    {
        return $this->hasMany(CampaignInvite::class);
    }

    public function pendingInvites()
    {
        return $this->invites()->where('status', CampaignInvite::STATUS_PENDING);
    }
}
