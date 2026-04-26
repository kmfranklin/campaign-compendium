<?php

namespace App\Http\Controllers;

use App\Models\Creature;
use App\Models\CreatureType;
use Illuminate\Http\Request;

class CreatureController extends Controller
{
    /**
     * Display a paginated, filterable list of SRD creatures.
     *
     * Accessible to everyone (no auth required). Custom creature CRUD will be
     * added in a later phase behind auth middleware.
     */
    public function index(Request $request)
    {
        $query = Creature::with('type')
            ->where('is_srd', true)
            ->orderBy('name');

        // Name search
        if ($search = $request->q) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Type filter
        if ($type = $request->type) {
            $query->whereHas('type', fn ($q) => $q->where('slug', $type));
        }

        // CR filter — stored as a string ('1/8', '1/4', '1/2', '1', '2', etc.)
        if ($cr = $request->cr) {
            $query->where('challenge_rating', $cr);
        }

        // Size filter
        if ($size = $request->size) {
            $query->where('size', $size);
        }

        $creatures = $query->paginate(20)->appends($request->except('page'));

        // Data for filter dropdowns
        $types = CreatureType::orderBy('name')->get();
        $crs   = $this->crOptions();
        $sizes = $this->sizeOptions();

        if ($request->ajax()) {
            return view('creatures.partials.results', compact('creatures'))->render();
        }

        return view('creatures.index', compact('creatures', 'types', 'crs', 'sizes'));
    }

    /**
     * Display a single creature's full statblock.
     */
    public function show(Creature $creature)
    {
        $creature->load('type');

        return view('creatures.show', compact('creature'));
    }

    /**
     * CR values in display order (fractional first, then ascending integers).
     */
    private function crOptions(): array
    {
        return [
            '0'   => '0',
            '1/8' => '1/8',
            '1/4' => '1/4',
            '1/2' => '1/2',
            '1'   => '1',
            '2'   => '2',
            '3'   => '3',
            '4'   => '4',
            '5'   => '5',
            '6'   => '6',
            '7'   => '7',
            '8'   => '8',
            '9'   => '9',
            '10'  => '10',
            '11'  => '11',
            '12'  => '12',
            '13'  => '13',
            '14'  => '14',
            '15'  => '15',
            '16'  => '16',
            '17'  => '17',
            '18'  => '18',
            '19'  => '19',
            '20'  => '20',
            '21'  => '21',
            '22'  => '22',
            '23'  => '23',
            '24'  => '24',
            '30'  => '30',
        ];
    }

    /**
     * Size options in D&D size order.
     */
    private function sizeOptions(): array
    {
        return [
            'tiny'       => 'Tiny',
            'small'      => 'Small',
            'medium'     => 'Medium',
            'large'      => 'Large',
            'huge'       => 'Huge',
            'gargantuan' => 'Gargantuan',
        ];
    }
}
