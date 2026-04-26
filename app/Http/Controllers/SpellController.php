<?php

namespace App\Http\Controllers;

use App\Models\Spell;
use App\Models\SpellSchool;
use Illuminate\Http\Request;

class SpellController extends Controller
{
    /**
     * Display a paginated, filterable list of SRD spells.
     *
     * Accessible to everyone (no auth required). Custom spell CRUD will be
     * added in a later phase behind auth middleware.
     */
    public function index(Request $request)
    {
        $query = Spell::with('school')
            ->where('is_srd', true)
            ->orderBy('level')
            ->orderBy('name');

        // Name search
        if ($search = $request->q) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Level filter (0 = cantrip)
        if ($request->filled('level')) {
            $query->where('level', (int) $request->level);
        }

        // School filter
        if ($school = $request->school) {
            $query->whereHas('school', fn ($q) => $q->where('slug', $school));
        }

        // Class filter — classes is a JSON array column, so we use LIKE to check
        // for the presence of a specific class slug within the stored JSON string.
        // e.g. WHERE classes LIKE '%"srd_wizard"%'
        if ($class = $request->class) {
            $query->where('classes', 'like', '%"' . $class . '"%');
        }

        // Casting time filter
        if ($castingTime = $request->casting_time) {
            $query->where('casting_time', $castingTime);
        }

        $spells = $query->paginate(20)->appends($request->except('page'));

        // Data for filter dropdowns
        $schools      = SpellSchool::orderBy('name')->get();
        $castingTimes = $this->castingTimeOptions();
        $classes      = $this->classOptions();

        if ($request->ajax()) {
            return view('spells.partials.results', compact('spells'))->render();
        }

        return view('spells.index', compact('spells', 'schools', 'castingTimes', 'classes'));
    }

    /**
     * Display a single spell.
     */
    public function show(Spell $spell)
    {
        $spell->load('school');

        return view('spells.show', compact('spell'));
    }

    /**
     * The seven SRD spellcasting classes, keyed by their JSON slug.
     */
    private function classOptions(): array
    {
        return [
            'srd_bard'     => 'Bard',
            'srd_cleric'   => 'Cleric',
            'srd_druid'    => 'Druid',
            'srd_ranger'   => 'Ranger',
            'srd_sorcerer' => 'Sorcerer',
            'srd_warlock'  => 'Warlock',
            'srd_wizard'   => 'Wizard',
        ];
    }

    /**
     * Human-readable casting time options for the filter dropdown.
     */
    private function castingTimeOptions(): array
    {
        return [
            'action'       => '1 Action',
            'bonus-action' => '1 Bonus Action',
            'reaction'     => '1 Reaction',
            '1minute'      => '1 Minute',
            '10minutes'    => '10 Minutes',
            '1hour'        => '1 Hour',
            '8hours'       => '8 Hours',
            '12hours'      => '12 Hours',
            '24hours'      => '24 Hours',
        ];
    }
}
