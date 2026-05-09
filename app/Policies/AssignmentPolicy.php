<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Determine if the user can view the assignment.
     */
    public function view(User $user, Assignment $assignment): bool
    {
        // Admin can view all assignments
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$assignment->relationLoaded('course')) {
            $assignment->load('course');
        }

        // Guru and dosen can view assignments from their own courses
        if (($user->isGuru() || $user->isDosen()) && $assignment->course->instructor_id === $user->id) {
            return true;
        }

        // Siswa and mahasiswa can view published assignments from enrolled courses
        if (($user->isSiswa() || $user->isMahasiswa()) && $assignment->course) {
            return $assignment->is_published && $assignment->course->isEnrolledBy($user);
        }

        return false;
    }

    /**
     * Determine if the user can create assignments for a course.
     */
    public function create(User $user, Course $course): bool
    {
        // Admin can create assignments for any course
        if ($user->isAdmin()) {
            return true;
        }

        // Guru or dosen who owns the course can create assignments
        return ($user->isGuru() || $user->isDosen()) && $course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can update the assignment.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        // Admin can update all assignments
        if ($user->isAdmin()) {
            return true;
        }

        // Only the assignment creator can update
        return $assignment->created_by === $user->id;
    }

    /**
     * Determine if the user can delete the assignment.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        // Admin can delete all assignments
        if ($user->isAdmin()) {
            return true;
        }

        // Only the assignment creator can delete
        return $assignment->created_by === $user->id;
    }

    /**
     * Determine if the user can submit to the assignment.
     */
    public function submit(User $user, Assignment $assignment): bool
    {
        // Only siswa/mahasiswa can submit
        if (!$user->isSiswa() && !$user->isMahasiswa()) {
            return false;
        }

        // Assignment must be published
        if (!$assignment->is_published) {
            return false;
        }

        // Ensure course relationship is loaded
        if (!$assignment->relationLoaded('course')) {
            $assignment->load('course');
        }

        // User must be enrolled in the course
        $isEnrolled = Enrollment::where('course_id', $assignment->course_id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return false;
        }

        // Assignment must be able to accept submissions
        return $assignment->canAcceptSubmission();
    }

    /**
     * Determine if the user can grade submissions for the assignment.
     */
    public function grade(User $user, Assignment $assignment): bool
    {
        // Admin can grade all assignments
        if ($user->isAdmin()) {
            return true;
        }

        // Only the assignment creator (guru/dosen) can grade
        return ($user->isGuru() || $user->isDosen()) && $assignment->created_by === $user->id;
    }
}
