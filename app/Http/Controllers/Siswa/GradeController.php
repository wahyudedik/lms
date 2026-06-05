<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        // Get enrolled courses
        $enrolledCourses = Auth::user()->enrollments()
            ->with('course')
            ->get()
            ->pluck('course');

        // Build query for grades
        $query = Enrollment::with(['course.instructor', 'course.exams'])
            ->where('user_id', Auth::id());

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $enrollments = $query->paginate(10);

        // Eager load all attempts once to avoid N+1 query problem (Bug #23)
        $allAttempts = ExamAttempt::where('user_id', Auth::id())
            ->whereIn('status', ['submitted', 'graded'])
            ->get()
            ->groupBy('exam_id');

        // Calculate grades for each enrollment
        $grades = $enrollments->map(function ($enrollment) use ($allAttempts) {
            $exams = $enrollment->course->exams;
            $examIds = $exams->pluck('id');

            // Filter attempts for this course's exams from pre-loaded collection
            $attempts = $allAttempts->filter(fn ($attempts, $examId) => $examIds->contains($examId))
                ->flatten(1);

            // Get best attempt for each exam
            $bestAttempts = $attempts->groupBy('exam_id')->map(function ($examAttempts) {
                return $examAttempts->sortByDesc('score')->first();
            });

            $finalGrade = $bestAttempts->avg('score') ?? 0;

            return (object) [
                'course' => $enrollment->course,
                'enrollment' => $enrollment,
                'total_exams' => $exams->count(),
                'completed_exams' => $bestAttempts->count(),
                'final_grade' => $finalGrade,
                'last_activity' => $attempts->max('submitted_at'),
            ];
        });

        // Statistics
        $allGrades = $grades->pluck('final_grade')->filter();
        $stats = [
            'total_courses' => $enrollments->total(),
            'average_grade' => $allGrades->avg() ?? 0,
            'highest_grade' => $allGrades->max() ?? 0,
            'lowest_grade' => $allGrades->min() ?? 0,
        ];

        return view('siswa.grades.index', compact('grades', 'enrolledCourses', 'stats', 'enrollments'));
    }

    public function show(Enrollment $enrollment)
    {
        // Bug #26: Use policy instead of manual check
        $this->authorize('view', $enrollment);

        $enrollment->load('course.instructor');

        // Get all exams for this course
        $exams = $enrollment->course->exams;

        // Get all exam attempts
        $examGrades = ExamAttempt::with('exam')
            ->where('user_id', Auth::id())
            ->whereIn('exam_id', $exams->pluck('id'))
            ->whereIn('status', ['submitted', 'graded'])
            ->get()
            ->groupBy('exam_id')
            ->map(function ($attempts) {
                // Return best attempt for each exam
                return $attempts->sortByDesc('score')->first();
            })
            ->values();

        // Calculate final grade
        $finalGrade = $examGrades->avg('score') ?? 0;

        $grade = (object) [
            'course' => $enrollment->course,
            'enrollment' => $enrollment,
            'enrolled_at' => $enrollment->created_at,
            'final_grade' => $finalGrade,
            'progress' => $enrollment->progress ?? 0,
            'last_activity' => $examGrades->max('submitted_at'),
            'certificate' => $enrollment->certificate,
        ];

        return view('siswa.grades.show', compact('grade', 'examGrades'));
    }
}
