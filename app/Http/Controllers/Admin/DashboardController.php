<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_exams' => Exam::count(),
            'active_students' => User::where('role', 'siswa')->where('is_active', true)->count(),
            'total_enrollments' => Enrollment::count(),
            'total_attempts' => ExamAttempt::count(),
            'avg_exam_score' => ExamAttempt::where('status', 'graded')->avg('score') ?? 0,
        ];

        // Recent users (last 5)
        $recentUsers = User::latest()->take(5)->get();

        // Recent courses (last 5)
        $recentCourses = Course::with('instructor')->latest()->take(5)->get();

        // Recent exam attempts (last 5)
        $recentAttempts = ExamAttempt::with(['user', 'exam'])
            ->where('status', 'graded')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Active exams (currently available)
        $activeExams = Exam::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', now());
            })
            ->withCount('questions')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCourses', 'recentAttempts', 'activeExams'));
    }
}
