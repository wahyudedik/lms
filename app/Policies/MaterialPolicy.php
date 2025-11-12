<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    /**
     * Determine if the user can view any materials.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view materials list
        return true;
    }

    /**
     * Determine if the user can view the material.
     */
    public function view(User $user, Material $material): bool
    {
        // Admin can view all materials
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$material->relationLoaded('course')) {
            $material->load('course');
        }

        // Guru can view materials from their own courses
        if ($user->isGuru() && $material->course->instructor_id === $user->id) {
            return true;
        }

        // Siswa can view materials from enrolled courses
        if ($user->isSiswa() && $material->course) {
            return $material->course->isEnrolledBy($user);
        }

        return false;
    }

    /**
     * Determine if the user can create materials.
     */
    public function create(User $user): bool
    {
        // Admin and guru can create materials
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine if the user can update the material.
     */
    public function update(User $user, Material $material): bool
    {
        // Admin can update all materials
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$material->relationLoaded('course')) {
            $material->load('course');
        }

        // Guru can only update materials from their own courses
        return $user->isGuru() && $material->course && $material->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the material.
     */
    public function delete(User $user, Material $material): bool
    {
        // Same as update
        return $this->update($user, $material);
    }
}

