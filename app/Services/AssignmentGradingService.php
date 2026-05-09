<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseGradeWeight;
use App\Models\Enrollment;
use App\Models\ExamAttempt;
use App\Models\User;

class AssignmentGradingService
{
    /**
     * Grade a submission with score and optional feedback.
     *
     * Updates submission with score, feedback, graded_by, graded_at, status,
     * and calculates final_score with penalty if applicable.
     * Dispatches SubmissionGraded notification to the student.
     */
    public function grade(AssignmentSubmission $submission, int $score, ?string $feedback, User $grader): AssignmentSubmission
    {
        $finalScore = $this->calculateFinalScore($score, $submission);

        $submission->update([
            'score' => $score,
            'feedback' => $feedback,
            'graded_by' => $grader->id,
            'graded_at' => now(),
            'status' => 'graded',
            'final_score' => $finalScore,
        ]);

        // Dispatch SubmissionGraded notification to the student
        $submission->user->notify(new \App\Notifications\SubmissionGraded($submission));

        return $submission;
    }

    /**
     * Calculate the final score after applying penalty if applicable.
     *
     * If the submission has a penalty_applied, the final score is:
     * score - (score × penalty_applied / 100), rounded to 2 decimal places.
     * Otherwise, the final score equals the raw score.
     */
    public function calculateFinalScore(int $score, AssignmentSubmission $submission): float
    {
        if ($submission->penalty_applied) {
            $deduction = $score * $submission->penalty_applied / 100;

            return round($score - $deduction, 2);
        }

        return (float) $score;
    }

    /**
     * Get submission statistics for an assignment.
     *
     * Returns an array with total_enrolled, submitted_count, graded_count,
     * and not_submitted_count.
     */
    public function getSubmissionStatistics(Assignment $assignment): array
    {
        $totalEnrolled = Enrollment::where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->count();

        $submittedCount = $assignment->submissions()->count();

        $gradedCount = $assignment->submissions()
            ->where('status', 'graded')
            ->count();

        $notSubmittedCount = $totalEnrolled - $submittedCount;

        return [
            'total_enrolled' => $totalEnrolled,
            'submitted_count' => $submittedCount,
            'graded_count' => $gradedCount,
            'not_submitted_count' => max(0, $notSubmittedCount),
        ];
    }

    /**
     * Calculate the weighted course grade for a student.
     *
     * Computes: (avg_assignment_final_score × assignment_weight / 100)
     *         + (avg_exam_score × exam_weight / 100)
     */
    public function calculateCourseGrade(User $student, Course $course): float
    {
        $gradeWeight = CourseGradeWeight::getForCourse($course->id);

        // Calculate average assignment final_score for the student in this course
        $avgAssignmentScore = AssignmentSubmission::whereHas('assignment', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->where('user_id', $student->id)
            ->where('status', 'graded')
            ->avg('final_score') ?? 0;

        // Calculate average exam score from exam_attempts for this course
        $avgExamScore = ExamAttempt::whereHas('exam', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->where('user_id', $student->id)
            ->where('status', 'graded')
            ->avg('score') ?? 0;

        $finalGrade = ($avgAssignmentScore * $gradeWeight->assignment_weight / 100)
            + ($avgExamScore * $gradeWeight->exam_weight / 100);

        return round($finalGrade, 2);
    }

    /**
     * Batch recalculate all student grades for a course.
     *
     * For each enrolled student, calls calculateCourseGrade.
     * Useful when grade weights are updated.
     */
    public function recalculateAllGrades(Course $course): void
    {
        $students = User::whereIn(
            'id',
            Enrollment::where('course_id', $course->id)
                ->where('status', 'active')
                ->pluck('user_id')
        )->get();

        foreach ($students as $student) {
            $this->calculateCourseGrade($student, $course);
        }
    }

    /**
     * Get the average final_score across all graded submissions for a course's assignments.
     */
    public function getAverageAssignmentScore(Course $course): float
    {
        $average = AssignmentSubmission::whereHas('assignment', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->where('status', 'graded')
            ->avg('final_score');

        return round((float) ($average ?? 0), 2);
    }

    /**
     * Get the student's completion rate for a course.
     *
     * Completion rate = number of submitted assignments / total published assignments in the course.
     */
    public function getStudentCompletionRate(User $student, Course $course): float
    {
        $totalPublishedAssignments = Assignment::where('course_id', $course->id)
            ->where('is_published', true)
            ->count();

        if ($totalPublishedAssignments === 0) {
            return 0.0;
        }

        $submittedCount = AssignmentSubmission::where('user_id', $student->id)
            ->whereHas('assignment', function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->where('is_published', true);
            })
            ->count();

        return round($submittedCount / $totalPublishedAssignments, 2);
    }
}
