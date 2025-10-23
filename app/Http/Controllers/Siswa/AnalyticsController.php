<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Display student analytics dashboard
     */
    public function index()
    {
        $student = auth()->user();

        // Get enrolled courses
        $enrolledCourses = $student->enrolledCourses()
            ->where('enrollments.status', 'active')
            ->get();

        // Get exam attempts
        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->with('exam.course')
            ->get();

        // Overall statistics
        $stats = [
            'total_courses' => $enrolledCourses->count(),
            'avg_progress' => $enrolledCourses->avg('pivot.progress') ?? 0,
            'total_exams_taken' => $attempts->count(),
            'avg_score' => $attempts->avg('score') ?? 0,
            'exams_passed' => $attempts->filter(fn($a) => $a->passed)->count(),
            'exams_failed' => $attempts->filter(fn($a) => !$a->passed && $a->passed !== null)->count(),
        ];

        return view('siswa.analytics.index', compact('stats', 'enrolledCourses', 'attempts'));
    }

    /**
     * Get performance trend over time
     */
    public function performanceTrend()
    {
        $student = auth()->user();

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->orderBy('submitted_at')
            ->take(20)
            ->get();

        $result = [
            'labels' => $attempts->map(fn($a, $i) => 'Ujian ' . ($i + 1))->toArray(),
            'datasets' => [
                [
                    'label' => 'Nilai Saya',
                    'data' => $attempts->pluck('score')->toArray(),
                    'backgroundColor' => 'rgba(168, 85, 247, 0.2)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Target (KKM)',
                    'data' => $attempts->map(fn($a) => $a->exam->pass_score)->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'fill' => false,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get performance by course
     */
    public function performanceByCourse()
    {
        $student = auth()->user();

        $courses = $student->enrolledCourses()
            ->where('enrollments.status', 'active')
            ->with(['exams.attempts' => function ($q) use ($student) {
                $q->where('user_id', $student->id)->where('status', 'graded');
            }])
            ->get();

        $result = [
            'labels' => $courses->pluck('title')->map(fn($t) => \Str::limit($t, 20))->toArray(),
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => $courses->map(function ($course) {
                        $attempts = $course->exams->flatMap->attempts;
                        return $attempts->count() > 0 ? $attempts->avg('score') : 0;
                    })->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                    ],
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get exam pass/fail ratio
     */
    public function examPassFailRatio()
    {
        $student = auth()->user();

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->get();

        $passed = $attempts->filter(fn($a) => $a->passed)->count();
        $failed = $attempts->filter(fn($a) => !$a->passed && $a->passed !== null)->count();

        $result = [
            'labels' => ['Lulus', 'Tidak Lulus'],
            'datasets' => [
                [
                    'label' => 'Hasil Ujian',
                    'data' => [$passed, $failed],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get study time distribution (mock data - can be enhanced with actual tracking)
     */
    public function studyTimeDistribution()
    {
        $student = auth()->user();

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->get();

        // Group by day of week
        $dayDistribution = [
            'Senin' => 0,
            'Selasa' => 0,
            'Rabu' => 0,
            'Kamis' => 0,
            'Jumat' => 0,
            'Sabtu' => 0,
            'Minggu' => 0,
        ];

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($attempts as $attempt) {
            if ($attempt->submitted_at) {
                $dayName = $days[$attempt->submitted_at->dayOfWeek];
                $dayDistribution[$dayName]++;
            }
        }

        $result = [
            'labels' => array_keys($dayDistribution),
            'datasets' => [
                [
                    'label' => 'Aktivitas Belajar',
                    'data' => array_values($dayDistribution),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get recent exam scores (last 10)
     */
    public function recentExamScores()
    {
        $student = auth()->user();

        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->with('exam')
            ->orderByDesc('submitted_at')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        $result = [
            'labels' => $attempts->map(fn($a) => \Str::limit($a->exam->title, 15))->toArray(),
            'datasets' => [
                [
                    'label' => 'Nilai',
                    'data' => $attempts->pluck('score')->toArray(),
                    'backgroundColor' => $attempts->map(function ($a) {
                        return $a->passed ? 'rgba(34, 197, 94, 0.7)' : 'rgba(239, 68, 68, 0.7)';
                    })->toArray(),
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }
}
