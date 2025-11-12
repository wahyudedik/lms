<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine if the user can view any enrollments.
     */
    public function viewAny(User $user): bool
    {
        // Admin, guru, and siswa can view enrollments
        return true;
    }

    /**
     * Determine if the user can view the enrollment.
     */
    public function view(User $user, Enrollment $enrollment): bool
    {
        // Admin can view all enrollments
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$enrollment->relationLoaded('course')) {
            $enrollment->load('course');
        }

        // Guru can view enrollments from their own courses
        if ($user->isGuru() && $enrollment->course) {
            return $enrollment->course->instructor_id === $user->id;
        }

        // Siswa can view their own enrollments
        if ($user->isSiswa()) {
            return $enrollment->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create enrollments.
     */
    public function create(User $user): bool
    {
        // Admin, guru, and siswa can create enrollments
        // Guru can manually enroll students
        // Siswa can self-enroll (if allowed)
        return $user->isAdmin() || $user->isGuru() || $user->isSiswa();
    }

    /**
     * Determine if the user can update the enrollment.
     */
    public function update(User $user, Enrollment $enrollment): bool
    {
        // Admin can update all enrollments
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$enrollment->relationLoaded('course')) {
            $enrollment->load('course');
        }

        // Guru can update enrollments from their own courses
        if ($user->isGuru() && $enrollment->course) {
            return $enrollment->course->instructor_id === $user->id;
        }

        // Siswa cannot update enrollments (only status changes by guru)
        return false;
    }

    /**
     * Determine if the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        // Admin can delete all enrollments
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$enrollment->relationLoaded('course')) {
            $enrollment->load('course');
        }

        // Guru can delete enrollments from their own courses
        if ($user->isGuru() && $enrollment->course) {
            return $enrollment->course->instructor_id === $user->id;
        }

        // Siswa cannot delete enrollments
        return false;
    }

    /**
     * Determine if the user can generate certificate for enrollment.
     */
    public function generate(User $user, Enrollment $enrollment): bool
    {
        // Student can generate their own certificate
        if ($enrollment->user_id === $user->id) {
            return true;
        }

        // Admin can generate for any enrollment
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$enrollment->relationLoaded('course')) {
            $enrollment->load('course');
        }

        // Instructor (guru) of the course can generate
        if ($user->isGuru() && $enrollment->course) {
            return $enrollment->course->instructor_id === $user->id;
        }

        return false;
    }
}

