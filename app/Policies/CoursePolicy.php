<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine if the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view courses list
        return true;
    }

    /**
     * Determine if the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        // Admin can view all courses
        if ($user->isAdmin()) {
            return true;
        }

        // Guru can view their own courses
        if ($user->isGuru() && $course->instructor_id === $user->id) {
            return true;
        }

        // Siswa can view courses they are enrolled in
        if ($user->isSiswa()) {
            return $course->isEnrolledBy($user);
        }

        return false;
    }

    /**
     * Determine if the user can create courses.
     */
    public function create(User $user): bool
    {
        // Admin and guru can create courses
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine if the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin can update all courses
        if ($user->isAdmin()) {
            return true;
        }

        // Guru can only update their own courses
        return $user->isGuru() && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        // Same as update
        return $this->update($user, $course);
    }
}

