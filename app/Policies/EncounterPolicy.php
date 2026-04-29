<?php

namespace App\Policies;

use App\Models\Encounter;
use App\Models\User;

/**
 * Encounters are private to the user who saved them.
 * All policy methods enforce a simple ownership check:
 * only the encounter's creator may view, rename, or delete it.
 */
class EncounterPolicy
{
    /**
     * Any authenticated user can see their own encounter list.
     * The index query is already scoped to Auth::id() in the controller,
     * so this gate just needs to allow the page to render.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Encounter $encounter): bool
    {
        return $encounter->user_id === $user->id;
    }

    public function update(User $user, Encounter $encounter): bool
    {
        return $encounter->user_id === $user->id;
    }

    public function delete(User $user, Encounter $encounter): bool
    {
        return $encounter->user_id === $user->id;
    }
}
