<?php

namespace App\Policies;

use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    /**
     * Determine if the user can view any replies.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view replies
        return true;
    }

    /**
     * Determine if the user can view the reply.
     */
    public function view(User $user, ForumReply $reply): bool
    {
        // All authenticated users can view replies
        return true;
    }

    /**
     * Determine if the user can create replies.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create replies
        return true;
    }

    /**
     * Determine if the user can update the reply.
     */
    public function update(User $user, ForumReply $reply): bool
    {
        // Admin can update all replies
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can update their own reply
        return $reply->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the reply.
     */
    public function delete(User $user, ForumReply $reply): bool
    {
        // Admin can delete all replies
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can delete their own reply
        return $reply->user_id === $user->id;
    }

    /**
     * Determine if the user can mark reply as solution.
     */
    public function markAsSolution(User $user, ForumReply $reply): bool
    {
        // Admin and guru can mark any reply as solution
        if ($user->isAdmin() || $user->isGuru()) {
            return true;
        }

        // Thread owner can mark reply as solution
        if (!$reply->relationLoaded('thread')) {
            $reply->load('thread');
        }

        return $reply->thread && $reply->thread->user_id === $user->id;
    }
}

