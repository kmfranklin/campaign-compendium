<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creature extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'creature_type_id',
        'size',
        'alignment',
        'armor_class',
        'armor_detail',
        'hit_points',
        'hit_dice',
        'challenge_rating',
        'ability_score_strength',
        'ability_score_dexterity',
        'ability_score_constitution',
        'ability_score_intelligence',
        'ability_score_wisdom',
        'ability_score_charisma',
        'saving_throws',
        'skill_bonuses',
        'damage_immunities',
        'damage_resistances',
        'damage_vulnerabilities',
        'condition_immunities',
        'nonmagical_attack_immunity',
        'nonmagical_attack_resistance',
        'speed_walk',
        'speed_fly',
        'speed_swim',
        'speed_climb',
        'speed_burrow',
        'speed_hover',
        'sense_darkvision',
        'sense_blindsight',
        'sense_tremorsense',
        'sense_truesight',
        'sense_telepathy',
        'passive_perception',
        'languages_desc',
        'traits',
        'actions',
        'is_srd',
        'user_id',
        'base_creature_id',
    ];

    protected function casts(): array
    {
        return [
            'nonmagical_attack_immunity'  => 'boolean',
            'nonmagical_attack_resistance'=> 'boolean',
            'speed_hover'                 => 'boolean',
            'is_srd'                      => 'boolean',
            'saving_throws'               => 'array',
            'skill_bonuses'               => 'array',
            'damage_immunities'           => 'array',
            'damage_resistances'          => 'array',
            'damage_vulnerabilities'      => 'array',
            'condition_immunities'        => 'array',
            'traits'                      => 'array',
            'actions'                     => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function type(): BelongsTo
    {
        return $this->belongsTo(CreatureType::class, 'creature_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The SRD creature this was cloned from, if any.
     */
    public function baseCreature(): BelongsTo
    {
        return $this->belongsTo(self::class, 'base_creature_id');
    }

    /**
     * Custom creatures that were cloned from this one.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'base_creature_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Returns a formatted CR string: '1/8', '1/4', '1/2', or the integer value.
     * The raw value from the database is already a string, but this accessor
     * ensures a clean display value even for edge cases like '0' or '10.000'.
     */
    public function getCrDisplayAttribute(): string
    {
        return match ($this->challenge_rating) {
            '0.125', '0.12500', '1/8' => '1/8',
            '0.25',  '0.25000', '1/4' => '1/4',
            '0.5',   '0.50000', '1/2' => '1/2',
            default  => rtrim(rtrim($this->challenge_rating ?? '—', '0'), '.'),
        };
    }

    /**
     * Returns the XP value for this creature based on its Challenge Rating.
     *
     * The CR→XP table is fixed by the SRD/DMG and never changes, so we compute
     * it here rather than storing a redundant column. The fractional CRs stored
     * as strings ('1/8', '1/4', '1/2') are normalised to their decimal keys
     * before lookup. Returns 0 for CR 0 or any unrecognised value.
     *
     * This accessor is used by the encounter generator so that
     * the JSON response always includes a ready-to-use xp value.
     */
    public function getXpAttribute(): int
    {
        $cr = match ($this->challenge_rating) {
            '1/8', '0.125', '0.12500' => '0.125',
            '1/4', '0.25',  '0.25000' => '0.25',
            '1/2', '0.5',   '0.50000' => '0.5',
            default => $this->challenge_rating ?? '0',
        };

        return self::XP_BY_CR[$cr] ?? 0;
    }

    /**
     * Fixed DMG table mapping Challenge Rating to XP award.
     * Fractional CRs use their decimal string representation as keys.
     */
    public const XP_BY_CR = [
        '0'    => 10,
        '0.125'=> 25,
        '0.25' => 50,
        '0.5'  => 100,
        '1'    => 200,
        '2'    => 450,
        '3'    => 700,
        '4'    => 1100,
        '5'    => 1800,
        '6'    => 2300,
        '7'    => 2900,
        '8'    => 3900,
        '9'    => 5000,
        '10'   => 5900,
        '11'   => 7200,
        '12'   => 8400,
        '13'   => 10000,
        '14'   => 11500,
        '15'   => 13000,
        '16'   => 15000,
        '17'   => 18000,
        '18'   => 20000,
        '19'   => 22000,
        '20'   => 25000,
        '21'   => 33000,
        '22'   => 41000,
        '23'   => 50000,
        '24'   => 62000,
        '25'   => 75000,
        '26'   => 90000,
        '27'   => 105000,
        '28'   => 120000,
        '29'   => 135000,
        '30'   => 155000,
    ];

    /**
     * Returns the ability score modifier for a given score value.
     * Standard D&D formula: floor((score - 10) / 2)
     */
    public static function modifier(int $score): int
    {
        return (int) floor(($score - 10) / 2);
    }

    /**
     * Returns a formatted modifier string with sign, e.g. '+3' or '-1'.
     */
    public static function modifierString(int $score): string
    {
        $mod = self::modifier($score);
        return ($mod >= 0 ? '+' : '') . $mod;
    }
}
