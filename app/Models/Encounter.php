<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encounter extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'name',
        'party',
        'monsters',
        'total_xp',
        'adjusted_xp',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'party'    => 'array',
            'monsters' => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The campaign this encounter belongs to, if any.
     * Null until the user explicitly links it — or if the campaign is deleted.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
