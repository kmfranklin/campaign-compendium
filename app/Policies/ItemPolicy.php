<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /**
     * Anyone can list SRD items.
     * Logged-in users can also see their own custom items.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Anyone (even guests) can view SRD items.
     * Only the owner (or admin) can view their own custom items.
     */
    public function view(?User $user, Item $item): bool
    {
        if ($item->is_srd) {
            return true;
        }

        return $user && ($item->user_id === $user->id || $user->isSuperAdmin());
    }

    /**
     * Only authenticated users can create custom items.
     */
    public function create(User $user): bool
    {
        return true; // if we got here, user is logged in
    }

    /**
     * Only the owner (or admin) can update a custom item.
     * SRD items are read-only.
     */
    public function update(User $user, Item $item): bool
    {
        if ($item->is_srd) {
            return false;
        }

        return $item->user_id === $user->id || $user->isSuperAdmin();
    }

    /**
     * Only the owner (or admin) can delete a custom item.
     * SRD items cannot be deleted.
     */
    public function delete(User $user, Item $item): bool
    {
        if ($item->is_srd) {
            return false;
        }

        return $item->user_id === $user->id || $user->isSuperAdmin();
    }
}
