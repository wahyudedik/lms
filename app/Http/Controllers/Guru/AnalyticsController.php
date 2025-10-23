<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Display guru analytics dashboard
     */
    public function index(Request $request)
    {
        $guru = auth()->user();

        // Get guru's courses
        $courses = $guru->teachingCourses;

        // Overall statistics
        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum(fn($c) => $c->enrollments->count()),
            'total_exams' => Exam::whereIn('course_id', $courses->pluck('id'))->count(),
            'total_attempts' => ExamAttempt::whereHas('exam', function ($q) use ($courses) {
                $q->whereIn('course_id', $courses->pluck('id'));
            })->count(),
            'avg_score' => ExamAttempt::whereHas('exam', function ($q) use ($courses) {
                $q->whereIn('course_id', $courses->pluck('id'));
            })->where('status', 'graded')->avg('score') ?? 0,
        ];

        return view('guru.analytics.index', compact('stats', 'courses'));
    }

    /**
     * Get student performance by course
     */
    public function studentPerformanceByCourse(Request $request)
    {
        $courseId = $request->input('course_id');
        $guru = auth()->user();

        if ($courseId) {
            $courses = Course::where('id', $courseId)
                ->where('instructor_id', $guru->id)
                ->get();
        } else {
            $courses = $guru->teachingCourses->take(5);
        }

        $result = [
            'labels' => $courses->pluck('title')->map(fn($t) => \Str::limit($t, 20))->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $courses->map(fn($c) => $c->enrollments->count())->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => $courses->map(function ($course) {
                        $attempts = ExamAttempt::whereHas('exam', function ($q) use ($course) {
                            $q->where('course_id', $course->id);
                        })->where('status', 'graded')->avg('score');
                        return $attempts ?? 0;
                    })->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                    'type' => 'line',
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get exam completion rate
     */
    public function examCompletionRate(Request $request)
    {
        $guru = auth()->user();
        $courseIds = $guru->teachingCourses->pluck('id');

        $exams = Exam::whereIn('course_id', $courseIds)
            ->with(['course', 'attempts'])
            ->take(10)
            ->get();

        $result = [
            'labels' => $exams->map(fn($e) => \Str::limit($e->title, 20))->toArray(),
            'datasets' => [
                [
                    'label' => 'Total Attempts',
                    'data' => $exams->map(fn($e) => $e->attempts->count())->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                ],
                [
                    'label' => 'Completed',
                    'data' => $exams->map(fn($e) => $e->attempts->where('status', 'graded')->count())->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                ],
                [
                    'label' => 'In Progress',
                    'data' => $exams->map(fn($e) => $e->attempts->where('status', 'in_progress')->count())->toArray(),
                    'backgroundColor' => 'rgba(251, 146, 60, 0.7)',
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get grade distribution for a specific exam
     */
    public function gradeDistribution(Request $request)
    {
        $examId = $request->input('exam_id');
        $guru = auth()->user();

        $exam = Exam::whereHas('course', function ($q) use ($guru) {
            $q->where('instructor_id', $guru->id);
        })->findOrFail($examId);

        $attempts = $exam->attempts()->where('status', 'graded')->get();

        // Group by grade ranges
        $ranges = [
            'A (90-100)' => $attempts->filter(fn($a) => $a->score >= 90)->count(),
            'B (80-89)' => $attempts->filter(fn($a) => $a->score >= 80 && $a->score < 90)->count(),
            'C (70-79)' => $attempts->filter(fn($a) => $a->score >= 70 && $a->score < 80)->count(),
            'D (60-69)' => $attempts->filter(fn($a) => $a->score >= 60 && $a->score < 70)->count(),
            'E (<60)' => $attempts->filter(fn($a) => $a->score < 60)->count(),
        ];

        $result = [
            'labels' => array_keys($ranges),
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => array_values($ranges),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get student engagement metrics
     */
    public function studentEngagementMetrics(Request $request)
    {
        $courseId = $request->input('course_id');
        $guru = auth()->user();

        $course = Course::where('instructor_id', $guru->id)
            ->findOrFail($courseId);

        $enrollments = $course->enrollments()
            ->with('student')
            ->take(10)
            ->get();

        $result = [
            'labels' => $enrollments->map(fn($e) => \Str::limit($e->student->name, 15))->toArray(),
            'datasets' => [
                [
                    'label' => 'Progress (%)',
                    'data' => $enrollments->pluck('progress')->toArray(),
                    'backgroundColor' => 'rgba(168, 85, 247, 0.6)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }
}
