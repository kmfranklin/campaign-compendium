<?php

namespace App\Http\Controllers;

use App\Models\Creature;
use App\Models\CreatureType;
use App\Models\Encounter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EncounterCalculatorController extends Controller
{
    // ─── XP Thresholds by Character Level ────────────────────────────────────
    //
    // Each entry is [easy, medium, hard, deadly] XP for a single character at
    // that level. Source: D&D 5e SRD / DMG "Determining XP Thresholds" table.

    private const XP_THRESHOLDS = [
        1  => [25,   50,   75,   100],
        2  => [50,   100,  150,  200],
        3  => [75,   150,  225,  400],
        4  => [125,  250,  375,  500],
        5  => [250,  500,  750,  1100],
        6  => [300,  600,  900,  1400],
        7  => [350,  750,  1100, 1700],
        8  => [450,  900,  1400, 2100],
        9  => [550,  1100, 1600, 2400],
        10 => [600,  1200, 1900, 2800],
        11 => [800,  1600, 2400, 3600],
        12 => [1000, 2000, 3000, 4500],
        13 => [1100, 2200, 3400, 5100],
        14 => [1250, 2500, 3800, 5700],
        15 => [1400, 2800, 4300, 6400],
        16 => [1600, 3200, 4800, 7200],
        17 => [2000, 3900, 5900, 8800],
        18 => [2100, 4200, 6300, 9500],
        19 => [2400, 4900, 7300, 10900],
        20 => [2800, 5700, 8500, 12700],
    ];

    // Column index within XP_THRESHOLDS for each difficulty name.
    private const DIFFICULTY_INDEX = [
        'easy'   => 0,
        'medium' => 1,
        'hard'   => 2,
        'deadly' => 3,
    ];

    // ─── Monster Count Multipliers ────────────────────────────────────────────
    //
    // Applied to total monster XP to get adjusted XP (difficulty check value).
    // Source: DMG "Modify Total XP for Multiple Monsters" table.
    // Each entry: [min monsters, max monsters (inclusive), base multiplier]

    private const COUNT_MULTIPLIERS = [
        [1,  1,  1.0],
        [2,  2,  1.5],
        [3,  6,  2.0],
        [7,  10, 2.5],
        [11, 14, 3.0],
        [15, PHP_INT_MAX, 4.0],
    ];

    // Party size adjustments: small party (≤2) bumps multiplier one step up;
    // large party (≥6) drops it one step down.
    private const MULTIPLIER_STEPS = [1.0, 1.5, 2.0, 2.5, 3.0, 4.0];

    // ─── CR → XP ─────────────────────────────────────────────────────────────
    //
    // Mirrored from Creature::XP_BY_CR. The controller needs its own copy so
    // it can do budget math without instantiating Creature models.

    private const XP_BY_CR = [
        '0'     => 10,    '0.125' => 25,   '0.25' => 50,   '0.5'  => 100,
        '1'     => 200,   '2'     => 450,  '3'    => 700,  '4'    => 1100,
        '5'     => 1800,  '6'     => 2300, '7'    => 2900, '8'    => 3900,
        '9'     => 5000,  '10'    => 5900, '11'   => 7200, '12'   => 8400,
        '13'    => 10000, '14'    => 11500,'15'   => 13000,'16'   => 15000,
        '17'    => 18000, '18'    => 20000,'19'   => 22000,'20'   => 25000,
        '21'    => 33000, '22'    => 41000,'23'   => 50000,'24'   => 62000,
        '25'    => 75000, '26'    => 90000,'27'   => 105000,'28'  => 120000,
        '29'    => 135000,'30'    => 155000,
    ];

    // ─── Public Routes ────────────────────────────────────────────────────────

    /**
     * Serve the encounter calculator page.
     *
     * Passes creature types so the view can render the type-filter chip list
     * without a round-trip. The calculator itself is public — no login required.
     */
    public function index(): View
    {
        $creatureTypes = CreatureType::orderBy('name')->get(['id', 'name']);

        return view('encounter-calculator.index', compact('creatureTypes'));
    }

    /**
     * Suggest encounter compositions for the given party and target difficulty.
     *
     * This is a POST endpoint so we can accept an array payload cleanly.
     * It is intentionally public — guests can generate suggestions, only saving
     * requires authentication.
     *
     * Request body (JSON or form):
     *   party[]       int[]   — level of each party member (1–20), min 1 entry
     *   difficulty    string  — easy|medium|hard|deadly
     *   xp_min        int?    — optional raw XP floor (for XP-based leveling)
     *   types[]       int[]?  — optional creature_type_id filter (multi-select)
     *
     * Response:
     *   target        object  — { difficulty, xp_low, xp_high, thresholds }
     *   suggestions   array   — up to 3 encounter combos (solo/group/horde)
     *   candidates    array   — browsable creature list matching CR/type filters
     */
    public function suggest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'party'     => ['required', 'array', 'min:1', 'max:20'],
            'party.*'   => ['required', 'integer', 'min:1', 'max:20'],
            'difficulty'=> ['required', 'in:easy,medium,hard,deadly'],
            'xp_min'    => ['nullable', 'integer', 'min:0'],
            'types'     => ['nullable', 'array'],
            'types.*'   => ['nullable', 'integer', 'exists:creature_types,id'],
        ]);

        $party      = $validated['party'];
        $difficulty = $validated['difficulty'];
        $xpMin      = $validated['xp_min'] ?? null;
        $typeIds    = $validated['types'] ?? [];
        $partySize  = count($party);

        // ── Step 1: compute XP thresholds for the whole party ─────────────────
        $thresholds = $this->partyThresholds($party);

        // ── Step 2: the XP range we're aiming to hit ─────────────────────────
        [$xpLow, $xpHigh] = $this->difficultyRange($thresholds, $difficulty);

        // ── Step 3: build suggestion combos and stamp a difficulty label ─────
        $suggestions = $this->buildSuggestions($xpLow, $xpHigh, $xpMin, $typeIds, $partySize);

        // Stamp each combo with the label computed from actual adjusted_xp so
        // the frontend can render an accurate badge without extra math.
        foreach ($suggestions as &$combo) {
            $combo['difficulty'] = $this->difficultyLabel($combo['adjusted_xp'], $thresholds);
        }
        unset($combo);

        // ── Step 4: browsable candidate list for manual additions ─────────────
        $candidates = $this->candidates($thresholds, $difficulty, $typeIds, $partySize);

        return response()->json([
            'target' => [
                'difficulty' => $difficulty,
                'xp_low'     => $xpLow,
                'xp_high'    => $xpHigh,
                'xp_min'     => $xpMin,
                'thresholds' => $thresholds,
            ],
            'suggestions' => $suggestions,
            'candidates'  => $candidates,
        ]);
    }

    /**
     * Save a completed encounter for the authenticated user.
     *
     * The frontend sends the full encounter state as JSON. We validate the
     * shape, compute nothing server-side (the frontend already has all the
     * calculated values), and persist it. The snapshot approach — storing
     * name/cr/xp on each monster entry — means the saved encounter remains
     * accurate even if a creature record changes later.
     */
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                      => ['nullable', 'string', 'max:100'],
            'party'                     => ['required', 'array', 'min:1'],
            'party.*'                   => ['required', 'integer', 'min:1', 'max:20'],
            'monsters'                  => ['required', 'array', 'min:1'],
            'monsters.*.source'         => ['required', 'in:srd,custom_creature,manual'],
            'monsters.*.creature_id'    => ['nullable', 'integer'],
            'monsters.*.name'           => ['required', 'string', 'max:100'],
            'monsters.*.cr'             => ['nullable', 'string', 'max:10'],
            'monsters.*.xp'             => ['required', 'integer', 'min:0'],
            'monsters.*.quantity'       => ['required', 'integer', 'min:1', 'max:99'],
            'total_xp'                  => ['required', 'integer', 'min:0'],
            'adjusted_xp'               => ['required', 'integer', 'min:0'],
            'difficulty'                => ['required', 'in:trivial,easy,medium,hard,deadly'],
        ]);

        $encounter = Encounter::create([
            'user_id'     => Auth::id(),
            'name'        => $validated['name'] ?? null,
            'party'       => $validated['party'],
            'monsters'    => $validated['monsters'],
            'total_xp'    => $validated['total_xp'],
            'adjusted_xp' => $validated['adjusted_xp'],
            'difficulty'  => $validated['difficulty'],
        ]);

        return response()->json([
            'message'      => 'Encounter saved.',
            'encounter_id' => $encounter->id,
        ], 201);
    }

    /**
     * Return a compact stat block for a single creature, used by the
     * encounter calculator's inline stat drawer.
     *
     * Public endpoint — anyone can view SRD creature stats. Authenticated
     * users can also view their own custom creatures (same scoping as the
     * suggest endpoint). Returns 404 if the creature isn't visible.
     */
    public function creatureStats(Creature $creature): JsonResponse
    {
        // Enforce the same visibility rule used everywhere else: SRD is public,
        // custom creatures are visible only to their owner.
        if (! $creature->is_srd) {
            if (! Auth::check() || Auth::id() !== $creature->user_id) {
                abort(404);
            }
        }

        // Build a speed string, e.g. "30 ft., fly 60 ft."
        $speedParts = [];
        if ($creature->speed_walk)   $speedParts[] = $creature->speed_walk . ' ft.';
        if ($creature->speed_fly)    $speedParts[] = 'fly ' . $creature->speed_fly . ' ft.' . ($creature->speed_hover ? ' (hover)' : '');
        if ($creature->speed_swim)   $speedParts[] = 'swim ' . $creature->speed_swim . ' ft.';
        if ($creature->speed_climb)  $speedParts[] = 'climb ' . $creature->speed_climb . ' ft.';
        if ($creature->speed_burrow) $speedParts[] = 'burrow ' . $creature->speed_burrow . ' ft.';
        $speed = implode(', ', $speedParts) ?: '—';

        // Ability score modifier helper
        $mod = fn (int $score): string =>
            ($score >= 10 ? '+' : '') . (int) floor(($score - 10) / 2);

        return response()->json([
            'id'           => $creature->id,
            'name'         => $creature->name,
            'cr'           => $creature->cr_display,
            'xp'           => $creature->xp,
            'type'         => $creature->type?->name ?? '—',
            'size'         => $creature->size  ?? '—',
            'alignment'    => $creature->alignment ?? '—',
            'ac'           => $creature->armor_class,
            'ac_detail'    => $creature->armor_detail,
            'hp'           => $creature->hit_points,
            'hit_dice'     => $creature->hit_dice,
            'speed'        => $speed,
            'abilities'    => [
                'str' => ['score' => $creature->ability_score_strength,     'mod' => $mod($creature->ability_score_strength)],
                'dex' => ['score' => $creature->ability_score_dexterity,    'mod' => $mod($creature->ability_score_dexterity)],
                'con' => ['score' => $creature->ability_score_constitution, 'mod' => $mod($creature->ability_score_constitution)],
                'int' => ['score' => $creature->ability_score_intelligence, 'mod' => $mod($creature->ability_score_intelligence)],
                'wis' => ['score' => $creature->ability_score_wisdom,       'mod' => $mod($creature->ability_score_wisdom)],
                'cha' => ['score' => $creature->ability_score_charisma,     'mod' => $mod($creature->ability_score_charisma)],
            ],
            'saving_throws'            => $creature->saving_throws ?? [],
            'skill_bonuses'            => $creature->skill_bonuses ?? [],
            'damage_immunities'        => $creature->damage_immunities ?? [],
            'damage_resistances'       => $creature->damage_resistances ?? [],
            'damage_vulnerabilities'   => $creature->damage_vulnerabilities ?? [],
            'condition_immunities'     => $creature->condition_immunities ?? [],
            'passive_perception'       => $creature->passive_perception,
            'languages'                => $creature->languages_desc,
            'source'                   => $creature->is_srd ? 'srd' : 'custom_creature',
            'url'                      => route('creatures.show', $creature),
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Sum the XP thresholds for every member of the party.
     *
     * Returns an array keyed by difficulty name: easy, medium, hard, deadly.
     * Levels outside 1–20 are clamped to the nearest valid entry.
     *
     * Example: a party of [3, 3, 4] returns
     *   ['easy' => 325, 'medium' => 650, 'hard' => 975, 'deadly' => 1400]
     */
    private function partyThresholds(array $party): array
    {
        $totals = ['easy' => 0, 'medium' => 0, 'hard' => 0, 'deadly' => 0];

        foreach ($party as $level) {
            $level = max(1, min(20, (int) $level));
            $row   = self::XP_THRESHOLDS[$level];

            foreach (self::DIFFICULTY_INDEX as $name => $idx) {
                $totals[$name] += $row[$idx];
            }
        }

        return $totals;
    }

    /**
     * Return [low, high] adjusted-XP bounds for the requested difficulty.
     *
     * "low" is the threshold for the requested difficulty; "high" is the
     * threshold for the *next* difficulty (or ×1.5 for deadly which has no
     * hard cap). Both values are in adjusted XP terms (multiplied).
     */
    private function difficultyRange(array $thresholds, string $difficulty): array
    {
        $order = ['easy', 'medium', 'hard', 'deadly'];
        $idx   = array_search($difficulty, $order, true);

        $low  = $thresholds[$difficulty];
        $high = isset($order[$idx + 1])
            ? $thresholds[$order[$idx + 1]] - 1
            : (int) round($thresholds['deadly'] * 1.5);

        return [$low, $high];
    }

    /**
     * Compute the DMG monster-count multiplier for a given number of monsters
     * and party size, including the small-party / large-party adjustment.
     *
     * @param  int  $count      Total number of monsters in the encounter
     * @param  int  $partySize  Number of player characters
     */
    private function multiplier(int $count, int $partySize): float
    {
        // Find base multiplier from the count bracket table
        $base = 1.0;
        foreach (self::COUNT_MULTIPLIERS as [$min, $max, $mult]) {
            if ($count >= $min && $count <= $max) {
                $base = $mult;
                break;
            }
        }

        // Adjust for small or large parties
        $steps   = self::MULTIPLIER_STEPS;
        $stepIdx = array_search($base, $steps, true);

        if ($partySize <= 2 && $stepIdx !== false && $stepIdx < count($steps) - 1) {
            $base = $steps[$stepIdx + 1];  // bump up one step for tiny parties
        } elseif ($partySize >= 6 && $stepIdx > 0) {
            $base = $steps[$stepIdx - 1];  // drop down one step for big parties
        }

        return $base;
    }

    /**
     * Classify an adjusted XP total against the party thresholds.
     *
     * Returns 'trivial', 'easy', 'medium', 'hard', or 'deadly'.
     */
    private function difficultyLabel(int $adjustedXp, array $thresholds): string
    {
        if ($adjustedXp < $thresholds['easy'])   return 'trivial';
        if ($adjustedXp < $thresholds['medium']) return 'easy';
        if ($adjustedXp < $thresholds['hard'])   return 'medium';
        if ($adjustedXp < $thresholds['deadly']) return 'hard';
        return 'deadly';
    }

    /**
     * Normalise a challenge_rating string to the decimal key used in XP_BY_CR.
     *
     * The database may store '1/8', '1/4', '1/2' or '0.125', '0.25', '0.5'.
     * This returns the canonical decimal string so array lookups always work.
     */
    private function normaliseCr(string $cr): string
    {
        return match ($cr) {
            '1/8', '0.125', '0.12500' => '0.125',
            '1/4', '0.25',  '0.25000' => '0.25',
            '1/2', '0.5',   '0.50000' => '0.5',
            default => rtrim(rtrim($cr, '0'), '.'),
        };
    }

    /**
     * Return every CR string (both fraction and decimal forms) whose XP value
     * falls within [$minXp, $maxXp] inclusive.
     *
     * Used to constrain creature queries to creatures that are roughly the
     * right power level for a given budget.
     */
    private function crStringsForXpRange(int $minXp, int $maxXp): array
    {
        // Fraction aliases — stored in DB as either form
        $aliases = [
            '0.125' => ['0.125', '1/8'],
            '0.25'  => ['0.25',  '1/4'],
            '0.5'   => ['0.5',   '1/2'],
        ];

        $strings = [];
        foreach (self::XP_BY_CR as $cr => $xp) {
            if ($xp >= $minXp && $xp <= $maxXp) {
                if (isset($aliases[$cr])) {
                    $strings = array_merge($strings, $aliases[$cr]);
                } else {
                    $strings[] = $cr;
                }
            }
        }

        return $strings;
    }

    /**
     * Base Eloquent query for creatures in specific CRs and optional type list.
     *
     * Always includes SRD creatures; authenticated users also see their own
     * custom creatures (the same visibility rule as the old search endpoint).
     *
     * @param  string[]  $crStrings  Allowed challenge_rating values
     * @param  int[]     $typeIds    creature_type_id filter (empty = no filter)
     */
    private function creatureQuery(array $crStrings, array $typeIds = []): Builder
    {
        $query = Creature::query()
            ->select(['id', 'name', 'challenge_rating', 'creature_type_id', 'is_srd', 'user_id'])
            ->whereIn('challenge_rating', $crStrings)
            ->where(function (Builder $q) {
                $q->where('is_srd', true);

                if (Auth::check()) {
                    $q->orWhere(function (Builder $q) {
                        $q->where('is_srd', false)
                          ->where('user_id', Auth::id());
                    });
                }
            });

        if (! empty($typeIds)) {
            $query->whereIn('creature_type_id', $typeIds);
        }

        return $query;
    }

    /**
     * Build the standard array shape for a single monster entry.
     *
     * This is the same structure the frontend stores and the save endpoint
     * persists, so keeping it centralised avoids drift.
     */
    private function monsterShape(Creature $c, int $qty = 1): array
    {
        return [
            'source'      => $c->is_srd ? 'srd' : 'custom_creature',
            'creature_id' => $c->id,
            'name'        => $c->name,
            'cr'          => $c->cr_display,
            'xp'          => $c->xp,
            'quantity'    => $qty,
        ];
    }

    /**
     * Attempt to build a Solo Boss encounter.
     *
     * Looks for a single creature whose XP roughly fills the target budget.
     * Budget is the raw monster XP needed so that adjusted XP (×1.0 for solo)
     * lands somewhere inside [xpLow, xpHigh].
     *
     * Returns null if no suitable creature can be found.
     */
    private function buildSoloCombo(int $xpLow, int $xpHigh, array $typeIds): ?array
    {
        // Solo multiplier is always 1.0, so raw XP = adjusted XP.
        $crStrings = $this->crStringsForXpRange($xpLow, $xpHigh);

        if (empty($crStrings)) {
            return null;
        }

        $creature = $this->creatureQuery($crStrings, $typeIds)
            ->inRandomOrder()
            ->first();

        if (! $creature) {
            return null;
        }

        $totalXp    = $creature->xp;
        $multiplier = $this->multiplier(1, 1); // always 1.0 solo

        return [
            'style'       => 'Solo Boss',
            'description' => 'One powerful monster that fills the full XP budget.',
            'monsters'    => [$this->monsterShape($creature, 1)],
            'total_xp'    => $totalXp,
            'adjusted_xp' => (int) round($totalXp * $multiplier),
            'multiplier'  => $multiplier,
        ];
    }

    /**
     * Attempt to build a small-group encounter (1 leader + minions).
     *
     * The leader gets ~40 % of the raw budget; each minion gets ~20 %.
     * Multiplier for 4 monsters is ×2.0 (adjusted in partySize logic).
     *
     * @param  int  $partySize  Used to get the correct count multiplier
     */
    private function buildGroupCombo(int $xpLow, int $xpHigh, array $typeIds, int $partySize): ?array
    {
        $count      = 4;  // 1 leader + 3 minions
        $multiplier = $this->multiplier($count, $partySize);

        // Work backwards: raw budget = adjusted budget ÷ multiplier
        $rawBudgetLow  = (int) floor($xpLow  / $multiplier);
        $rawBudgetHigh = (int) ceil($xpHigh / $multiplier);

        // Leader gets 40 %, minions 20 % each
        $leaderXp = (int) round(($rawBudgetLow + $rawBudgetHigh) / 2 * 0.40);
        $minionXp = (int) round(($rawBudgetLow + $rawBudgetHigh) / 2 * 0.20);

        // Give ±30 % flex on each piece so we get real results
        $leaderCrs = $this->crStringsForXpRange((int) ($leaderXp * 0.7), (int) ($leaderXp * 1.3));
        $minionCrs = $this->crStringsForXpRange((int) ($minionXp * 0.7), (int) ($minionXp * 1.3));

        if (empty($leaderCrs) || empty($minionCrs)) {
            return null;
        }

        $leader = $this->creatureQuery($leaderCrs, $typeIds)->inRandomOrder()->first();
        $minion = $this->creatureQuery($minionCrs, $typeIds)->inRandomOrder()->first();

        if (! $leader || ! $minion) {
            return null;
        }

        $totalXp    = $leader->xp + ($minion->xp * 3);
        $adjustedXp = (int) round($totalXp * $multiplier);

        return [
            'style'       => 'Small Group',
            'description' => 'A leader supported by a trio of minions.',
            'monsters'    => [
                $this->monsterShape($leader, 1),
                $this->monsterShape($minion, 3),
            ],
            'total_xp'    => $totalXp,
            'adjusted_xp' => $adjustedXp,
            'multiplier'  => $multiplier,
        ];
    }

    /**
     * Attempt to build a Horde encounter (2 guards + many minions).
     *
     * Guards get ~20 % of budget each; minions split the remaining 60 % as
     * a pool of 6. Multiplier for 8 monsters is ×2.5.
     */
    private function buildHordeCombo(int $xpLow, int $xpHigh, array $typeIds, int $partySize): ?array
    {
        $count      = 8;  // 2 guards + 6 minions
        $multiplier = $this->multiplier($count, $partySize);

        $rawBudgetLow  = (int) floor($xpLow  / $multiplier);
        $rawBudgetHigh = (int) ceil($xpHigh / $multiplier);
        $midBudget     = (int) round(($rawBudgetLow + $rawBudgetHigh) / 2);

        $guardXp  = (int) round($midBudget * 0.20);
        $minionXp = (int) round($midBudget * 0.10);  // 60% / 6 minions each

        $guardCrs  = $this->crStringsForXpRange((int) ($guardXp  * 0.7), (int) ($guardXp  * 1.3));
        $minionCrs = $this->crStringsForXpRange((int) ($minionXp * 0.7), (int) ($minionXp * 1.3));

        if (empty($guardCrs) || empty($minionCrs)) {
            return null;
        }

        $guard  = $this->creatureQuery($guardCrs,  $typeIds)->inRandomOrder()->first();
        $minion = $this->creatureQuery($minionCrs, $typeIds)->inRandomOrder()->first();

        if (! $guard || ! $minion) {
            return null;
        }

        $totalXp    = ($guard->xp * 2) + ($minion->xp * 6);
        $adjustedXp = (int) round($totalXp * $multiplier);

        return [
            'style'       => 'Horde',
            'description' => 'Two elite guards commanding a swarm of weaker monsters.',
            'monsters'    => [
                $this->monsterShape($guard,  2),
                $this->monsterShape($minion, 6),
            ],
            'total_xp'    => $totalXp,
            'adjusted_xp' => $adjustedXp,
            'multiplier'  => $multiplier,
        ];
    }

    /**
     * Build all three suggestion combos, filtering out nulls.
     *
     * If $xpMin is provided (for XP-based leveling), any suggestions whose
     * total raw XP falls below the minimum are discarded.
     *
     * Each returned combo also gets a 'difficulty' label computed from the
     * party thresholds so the frontend can display a badge accurately.
     */
    private function buildSuggestions(
        int $xpLow,
        int $xpHigh,
        ?int $xpMin,
        array $typeIds,
        int $partySize
    ): array {
        // We need thresholds for the difficulty label — compute from party here.
        // This is called after partyThresholds(), but we need a reference in the
        // private helper. Pass a placeholder and label in suggest() instead.
        // Actually: labelling requires thresholds, so we do it in suggest().
        // This method returns combos WITHOUT difficulty label — suggest() adds it.
        $combos = array_filter([
            $this->buildSoloCombo($xpLow, $xpHigh, $typeIds),
            $this->buildGroupCombo($xpLow, $xpHigh, $typeIds, $partySize),
            $this->buildHordeCombo($xpLow, $xpHigh, $typeIds, $partySize),
        ]);

        // Apply optional XP minimum (raw total_xp, not adjusted)
        if ($xpMin !== null && $xpMin > 0) {
            $combos = array_filter($combos, fn ($c) => $c['total_xp'] >= $xpMin);
        }

        return array_values($combos);
    }

    /**
     * Build a browsable list of candidate creatures for manual additions.
     *
     * Returns creatures whose CR falls in a generous ±1 difficulty band around
     * the target — not just the exact target range — so the DM has options to
     * tune up or down.
     *
     * Results are sorted by CR ascending then name ascending. The CR filter
     * already constrains the set to a narrow band so the total is naturally
     * manageable (typically 20–80 creatures); we cap at 200 as a safety net.
     * The frontend handles all further sorting and filtering client-side since
     * the full dataset lands in Alpine state after the suggest() response.
     *
     * The $partySize parameter is kept for potential future use (e.g. per-size
     * CR weighting) but is not used in the current implementation.
     */
    private function candidates(
        array $thresholds,
        string $difficulty,
        array $typeIds,
        int $partySize
    ): array {
        $order = ['easy', 'medium', 'hard', 'deadly'];
        $idx   = array_search($difficulty, $order, true);

        // Include one band below and one above the target difficulty.
        // Raw per-monster XP for a solo monster at these difficulty boundaries.
        // Using solo (×1.0) so the range is wide and we get plenty of results.
        $lowerDiff = $order[max(0, $idx - 1)];

        $xpLow  = $thresholds[$lowerDiff];
        $xpHigh = (int) round(
            isset($order[$idx + 2])
                ? $thresholds[$order[$idx + 2]]
                : $thresholds['deadly'] * 1.5
        );

        $crStrings = $this->crStringsForXpRange($xpLow, $xpHigh);

        if (empty($crStrings)) {
            return [];
        }

        // Sort by CR numerically (CAST to REAL for correct fractional ordering),
        // then alphabetically within each CR tier.
        return $this->creatureQuery($crStrings, $typeIds)
            ->orderByRaw('CAST(challenge_rating AS REAL) ASC')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->map(fn (Creature $c) => $this->monsterShape($c))
            ->values()
            ->toArray();
    }
}
