<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'session_date',
        'summary',
    ];

    protected $casts = [
        // Cast to a Carbon date (no time component) for clean display/comparison
        'session_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * NPCs who appeared in this session.
     */
    public function npcs()
    {
        return $this->belongsToMany(Npc::class, 'session_log_npc')
                    ->withTimestamps();
    }

    /**
     * Quests that advanced during this session.
     */
    public function quests()
    {
        return $this->belongsToMany(Quest::class, 'session_log_quest')
                    ->withTimestamps();
    }

    /**
     * The attached media file (audio recording, etc.).
     * morphOne = at most one file per session log.
     */
    public function media()
    {
        return $this->morphOne(Media::class, 'mediable');
    }
}
