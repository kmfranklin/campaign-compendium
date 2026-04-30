<?php

namespace App\Models;

use App\Enums\QuestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'notes',
        'status',
    ];

    /**
     * Cast status to the QuestStatus enum so $quest->status is always
     * a typed enum value rather than a raw string.
     */
    protected $casts = [
        'status' => QuestStatus::class,
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Each quest belongs to one campaign.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * NPCs involved in this quest.
     */
    public function npcs()
    {
        return $this->belongsToMany(Npc::class, 'npc_quest')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Encounters nested under this quest.
     */
    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }

    // -------------------------------------------------------------------------
    // Scopes — convenient query filters by status
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', QuestStatus::Active);
    }

    public function scopePlanned($query)
    {
        return $query->where('status', QuestStatus::Planned);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', QuestStatus::Completed);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', QuestStatus::Failed);
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', QuestStatus::Abandoned);
    }
}
