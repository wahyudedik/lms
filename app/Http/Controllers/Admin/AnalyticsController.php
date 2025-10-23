<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display admin analytics dashboard
     */
    public function index(Request $request)
    {
        // Date range filter (default last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_exams' => Exam::count(),
            'total_enrollments' => Enrollment::count(),
            'active_students' => User::where('role', 'siswa')->where('is_active', true)->count(),
            'active_courses' => Course::where('status', 'published')->count(),
            'total_attempts' => ExamAttempt::count(),
            'avg_exam_score' => ExamAttempt::where('status', 'graded')->avg('score') ?? 0,
        ];

        return view('admin.analytics.index', compact('stats', 'startDate', 'endDate'));
    }

    /**
     * Get user registration trend data
     */
    public function userRegistrationTrend(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $data = User::selectRaw('DATE(created_at) as date, role, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'role')
            ->orderBy('date')
            ->get()
            ->groupBy('role');

        $dates = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        $result = [
            'labels' => array_map(fn($d) => date('d M', strtotime($d)), $dates),
            'datasets' => []
        ];

        $colors = [
            'admin' => ['bg' => 'rgba(59, 130, 246, 0.2)', 'border' => 'rgb(59, 130, 246)'],
            'guru' => ['bg' => 'rgba(34, 197, 94, 0.2)', 'border' => 'rgb(34, 197, 94)'],
            'siswa' => ['bg' => 'rgba(168, 85, 247, 0.2)', 'border' => 'rgb(168, 85, 247)'],
        ];

        foreach (['admin', 'guru', 'siswa'] as $role) {
            $roleData = $data->get($role, collect());
            $counts = [];

            foreach ($dates as $date) {
                $dayData = $roleData->firstWhere('date', $date);
                $counts[] = $dayData ? $dayData->count : 0;
            }

            $result['datasets'][] = [
                'label' => ucfirst($role),
                'data' => $counts,
                'backgroundColor' => $colors[$role]['bg'],
                'borderColor' => $colors[$role]['border'],
                'borderWidth' => 2,
                'fill' => true,
            ];
        }

        return response()->json($result);
    }

    /**
     * Get course enrollment statistics
     */
    public function courseEnrollmentStats()
    {
        $courses = Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(10)
            ->get();

        $result = [
            'labels' => $courses->pluck('title')->map(fn($t) => \Str::limit($t, 20))->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $courses->pluck('enrollments_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(255, 99, 255, 0.7)',
                        'rgba(99, 255, 132, 0.7)',
                    ],
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get exam performance statistics
     */
    public function examPerformanceStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $exams = Exam::with(['attempts' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'graded')
                ->whereBetween('submitted_at', [$startDate, $endDate]);
        }])
            ->has('attempts')
            ->take(10)
            ->get();

        $result = [
            'labels' => $exams->pluck('title')->map(fn($t) => \Str::limit($t, 25))->toArray(),
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => $exams->map(fn($e) => $e->attempts->avg('score') ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Pass Rate (%)',
                    'data' => $exams->map(function ($e) {
                        $total = $e->attempts->count();
                        $passed = $e->attempts->filter(fn($a) => $a->passed)->count();
                        return $total > 0 ? ($passed / $total * 100) : 0;
                    })->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get user role distribution
     */
    public function userRoleDistribution()
    {
        $distribution = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        $result = [
            'labels' => $distribution->pluck('role')->map(fn($r) => ucfirst($r))->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah User',
                    'data' => $distribution->pluck('count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    'borderWidth' => 2,
                ]
            ]
        ];

        return response()->json($result);
    }

    /**
     * Get monthly activity statistics
     */
    public function monthlyActivityStats()
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        $enrollments = Enrollment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months)
            ->groupBy('month')
            ->pluck('count', 'month');

        $attempts = ExamAttempt::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $months)
            ->groupBy('month')
            ->pluck('count', 'month');

        $result = [
            'labels' => array_map(fn($m) => date('M Y', strtotime($m . '-01')), $months),
            'datasets' => [
                [
                    'label' => 'Enrollments',
                    'data' => array_map(fn($m) => $enrollments->get($m, 0), $months),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 3,
                    'fill' => true,
                ],
                [
                    'label' => 'Exam Attempts',
                    'data' => array_map(fn($m) => $attempts->get($m, 0), $months),
                    'backgroundColor' => 'rgba(168, 85, 247, 0.2)',
                    'borderColor' => 'rgb(168, 85, 247)',
                    'borderWidth' => 3,
                    'fill' => true,
                ]
            ]
        ];

        return response()->json($result);
    }
}
