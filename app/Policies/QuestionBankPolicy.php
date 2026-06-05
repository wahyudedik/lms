<?php

namespace App\Policies;

use App\Models\QuestionBank;
use App\Models\User;

class QuestionBankPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'guru', 'dosen']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QuestionBank $questionBank): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (in_array($user->role, ['guru', 'dosen'])) {
            return $questionBank->created_by === $user->id || $questionBank->is_shared;
        }

        return $questionBank->is_shared;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'guru', 'dosen']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QuestionBank $questionBank): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $questionBank->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QuestionBank $questionBank): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $questionBank->created_by === $user->id;
    }
}
