<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spell extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'level',
        'spell_school_id',
        'casting_time',
        'duration',
        'range_text',
        'verbal',
        'somatic',
        'material',
        'material_consumed',
        'material_specified',
        'concentration',
        'ritual',
        'description',
        'higher_level',
        'classes',
        'damage_types',
        'saving_throw_ability',
        'is_srd',
        'user_id',
        'base_spell_id',
    ];

    protected function casts(): array
    {
        return [
            'level'             => 'integer',
            'verbal'            => 'boolean',
            'somatic'           => 'boolean',
            'material'          => 'boolean',
            'material_consumed' => 'boolean',
            'concentration'     => 'boolean',
            'ritual'            => 'boolean',
            'is_srd'            => 'boolean',
            'classes'           => 'array',
            'damage_types'      => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function school(): BelongsTo
    {
        return $this->belongsTo(SpellSchool::class, 'spell_school_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The SRD spell this was cloned from, if any.
     */
    public function baseSpell(): BelongsTo
    {
        return $this->belongsTo(self::class, 'base_spell_id');
    }

    /**
     * Custom spells that were cloned from this one.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'base_spell_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Human-readable level label: 'Cantrip' for level 0, '1st-level' etc.
     */
    public function getLevelLabelAttribute(): string
    {
        if ($this->level === 0) {
            return 'Cantrip';
        }

        $suffixes = ['', 'st', 'nd', 'rd'];
        $suffix   = $this->level <= 3 ? $suffixes[$this->level] : 'th';

        return $this->level . $suffix . '-level';
    }

    /**
     * Human-readable casting time for display.
     */
    public function getCastingTimeLabelAttribute(): string
    {
        return match ($this->casting_time) {
            'action'       => '1 Action',
            'bonus-action' => '1 Bonus Action',
            'reaction'     => '1 Reaction',
            '1minute'      => '1 Minute',
            '10minutes'    => '10 Minutes',
            '1hour'        => '1 Hour',
            '8hours'       => '8 Hours',
            '12hours'      => '12 Hours',
            '24hours'      => '24 Hours',
            default        => ucfirst($this->casting_time ?? ''),
        };
    }

    /**
     * Strip the 'srd_' prefix from class slugs and return display names.
     * e.g. ['srd_wizard', 'srd_druid'] → ['Druid', 'Wizard']
     */
    public function getClassNamesAttribute(): array
    {
        return collect($this->classes ?? [])
            ->map(fn ($c) => ucfirst(str_replace('srd_', '', $c)))
            ->sort()
            ->values()
            ->all();
    }
}
