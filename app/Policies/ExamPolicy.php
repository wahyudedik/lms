<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    /**
     * Determine if the user can view any exams.
     */
    public function viewAny(User $user): bool
    {
        // Admin, guru, and dosen can view exams
        return $user->isAdmin() || $user->isGuru() || $user->isDosen();
    }

    /**
     * Determine if the user can view the exam.
     */
    public function view(User $user, Exam $exam): bool
    {
        // Admin can view all exams
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$exam->relationLoaded('course')) {
            $exam->load('course');
        }

        // Guru and dosen can view exams from their own courses
        if (($user->isGuru() || $user->isDosen()) && $exam->course) {
            return $exam->course->instructor_id === $user->id;
        }

        // Siswa and mahasiswa can view exams from enrolled courses
        if (($user->isSiswa() || $user->isMahasiswa()) && $exam->course) {
            return $exam->course->isEnrolledBy($user);
        }

        return false;
    }

    /**
     * Determine if the user can create exams.
     */
    public function create(User $user): bool
    {
        // Admin, guru, and dosen can create exams
        return $user->isAdmin() || $user->isGuru() || $user->isDosen();
    }

    /**
     * Determine if the user can update the exam.
     */
    public function update(User $user, Exam $exam): bool
    {
        // Admin can update all exams
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$exam->relationLoaded('course')) {
            $exam->load('course');
        }

        // Guru and dosen can only update exams from their own courses
        return ($user->isGuru() || $user->isDosen()) && $exam->course && $exam->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the exam.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Same as update
        return $this->update($user, $exam);
    }
}

