<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine if the user can view any questions.
     */
    public function viewAny(User $user): bool
    {
        // Admin and guru can view questions
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine if the user can view the question.
     */
    public function view(User $user, Question $question): bool
    {
        // Admin can view all questions
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure relationships are loaded to avoid N+1 queries
        if (!$question->relationLoaded('exam')) {
            $question->load('exam.course');
        }

        // Guru can view questions from their own exams
        if ($user->isGuru() && $question->exam && $question->exam->course) {
            return $question->exam->course->instructor_id === $user->id;
        }

        // Siswa can view questions from enrolled courses (when taking exam)
        if ($user->isSiswa() && $question->exam && $question->exam->course) {
            return $question->exam->course->isEnrolledBy($user);
        }

        return false;
    }

    /**
     * Determine if the user can create questions.
     */
    public function create(User $user): bool
    {
        // Admin and guru can create questions
        return $user->isAdmin() || $user->isGuru();
    }

    /**
     * Determine if the user can update the question.
     */
    public function update(User $user, Question $question): bool
    {
        // Admin can update all questions
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure relationships are loaded to avoid N+1 queries
        if (!$question->relationLoaded('exam')) {
            $question->load('exam.course');
        }

        // Guru can only update questions from their own exams
        return $user->isGuru() && $question->exam && $question->exam->course && $question->exam->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can delete the question.
     */
    public function delete(User $user, Question $question): bool
    {
        // Same as update
        return $this->update($user, $question);
    }
}

