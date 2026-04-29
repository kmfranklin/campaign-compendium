<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EncounterController extends Controller
{
    /**
     * Display a listing of the authenticated user's saved encounters.
     * Ordered newest-first; paginated so large libraries stay fast.
     */
    public function index()
    {
        $encounters = Encounter::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('encounters.index', compact('encounters'));
    }

    /**
     * Display the specified encounter.
     * Users may only view their own encounters — 403 otherwise.
     */
    public function show(Encounter $encounter)
    {
        $this->authorize('view', $encounter);

        return view('encounters.show', compact('encounter'));
    }

    /**
     * Rename the specified encounter.
     * Accepts a nullable name — passing an empty string clears the name back
     * to "Unnamed Encounter". Only the owning user may rename.
     */
    public function update(Request $request, Encounter $encounter)
    {
        $this->authorize('update', $encounter);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
        ]);

        $encounter->update([
            'name' => $validated['name'] ?: null,
        ]);

        // AJAX rename request — return JSON so the Alpine component can
        // update the page heading without a full reload.
        if ($request->expectsJson()) {
            return response()->json([
                'name' => $encounter->name,
            ]);
        }

        return back()->with('success', 'Encounter renamed.');
    }

    /**
     * Delete the specified encounter.
     * Only the owning user may delete; redirects to the index on success.
     */
    public function destroy(Encounter $encounter)
    {
        $this->authorize('delete', $encounter);

        $encounter->delete();

        return redirect()
            ->route('encounters.index')
            ->with('success', 'Encounter deleted.');
    }
}
