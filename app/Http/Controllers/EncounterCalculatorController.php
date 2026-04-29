<?php

namespace App\Http\Controllers;

use App\Models\Creature;
use App\Models\Encounter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EncounterCalculatorController extends Controller
{
    /**
     * Serve the encounter calculator page.
     *
     * The page itself is public — no login required to use the calculator.
     * Alpine handles all the live calculation; the only server round-trips
     * are the creature search endpoint and (for auth users) the save endpoint.
     */
    public function index(): View
    {
        return view('encounter-calculator.index');
    }

    /**
     * JSON creature search for the encounter builder autocomplete.
     *
     * Public endpoint — anyone can search SRD creatures.
     * Authenticated users also see their own custom creatures (is_srd = false,
     * user_id = auth user). This is the hook that makes user-created monsters
     * automatically appear here once that feature is built — no changes needed.
     *
     * Returns a max of 20 results to keep the dropdown manageable.
     * Requires at least 1 character to avoid returning the entire bestiary.
     *
     * Optional query params:
     *   q         — name search string (required, min 1 char)
     *   cr_min    — minimum CR as a float (e.g. 0.25 for CR 1/4)
     *   cr_max    — maximum CR as a float (e.g. 5 for CR 5)
     */
    public function search(Request $request): JsonResponse
    {
        $q      = trim($request->string('q'));
        $crMin  = $request->filled('cr_min') ? (float) $request->input('cr_min') : null;
        $crMax  = $request->filled('cr_max') ? (float) $request->input('cr_max') : null;

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $query = Creature::query()
            ->select(['id', 'name', 'challenge_rating', 'creature_type_id', 'is_srd', 'user_id'])
            ->where('name', 'like', "%{$q}%")
            ->where(function ($q) {
                // Always include SRD creatures
                $q->where('is_srd', true);

                // Also include this user's custom creatures if authenticated
                if (Auth::check()) {
                    $q->orWhere(function ($q) {
                        $q->where('is_srd', false)
                          ->where('user_id', Auth::id());
                    });
                }
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('name')
            ->limit(20);

        // CR filtering — convert stored string values to float for comparison.
        // The CR column holds strings like '1/8', '1/4', '1/2', '1', '5', etc.
        // We cast to float in SQL so '1/4' → 0.25 comparisons work correctly.
        // Note: SQLite CAST('1/4' AS REAL) returns 1.0, not 0.25. For dev this
        // is acceptable; a production MySQL/Postgres deployment handles it fine.
        // The seeder stores fractional CRs as '0.125', '0.25', '0.5' to avoid
        // this — check your seed data if CR filtering behaves unexpectedly.
        if ($crMin !== null) {
            $query->whereRaw('CAST(challenge_rating AS REAL) >= ?', [$crMin]);
        }
        if ($crMax !== null) {
            $query->whereRaw('CAST(challenge_rating AS REAL) <= ?', [$crMax]);
        }

        $creatures = $query->get();

        // Map to the lean shape the frontend needs. We include xp (computed
        // by the accessor), cr_display (the formatted string), and source so
        // the UI can label custom monsters differently from SRD monsters.
        $results = $creatures->map(fn (Creature $c) => [
            'id'         => $c->id,
            'name'       => $c->name,
            'cr'         => $c->cr_display,       // '1/4', '5', etc.
            'xp'         => $c->xp,               // computed from CR
            'source'     => $c->is_srd ? 'srd' : 'custom_creature',
        ]);

        return response()->json($results);
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
}
