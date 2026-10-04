<?php

namespace App\Policies;

use App\Models\InformationCard;
use App\Models\User;

class InformationCardPolicy
{
    /**
     * Determine whether the user can view any information cards.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru() || $user->isDosen();
    }

    /**
     * Determine whether the user can view the information card.
     */
    public function view(User $user, InformationCard $informationCard): bool
    {
        return $user->isAdmin() || $user->isGuru() || $user->isDosen();
    }

    /**
     * Determine whether the user can create information cards.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isGuru() || $user->isDosen();
    }

    /**
     * Determine whether the user can update the information card.
     */
    public function update(User $user, InformationCard $informationCard): bool
    {
        return $user->isAdmin() || $informationCard->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the information card.
     */
    public function delete(User $user, InformationCard $informationCard): bool
    {
        return $user->isAdmin() || $informationCard->created_by === $user->id;
    }
}
