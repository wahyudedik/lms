<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    /**
     * Determine if the user can view any threads.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view threads
        return true;
    }

    /**
     * Determine if the user can view the thread.
     */
    public function view(User $user, ForumThread $thread): bool
    {
        // All authenticated users can view threads
        return true;
    }

    /**
     * Determine if the user can create threads.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create threads
        return true;
    }

    /**
     * Determine if the user can update the thread.
     */
    public function update(User $user, ForumThread $thread): bool
    {
        // Admin can update all threads
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can update their own thread
        return $thread->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the thread.
     */
    public function delete(User $user, ForumThread $thread): bool
    {
        // Admin can delete all threads
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can delete their own thread
        return $thread->user_id === $user->id;
    }

    /**
     * Determine if the user can pin the thread.
     */
    public function pin(User $user, ForumThread $thread): bool
    {
        // Admin and guru can pin threads
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine if the user can lock the thread.
     */
    public function lock(User $user, ForumThread $thread): bool
    {
        // Admin and guru can lock threads
        return $user->isAdmin() || $user->isGuru();
    }
}

