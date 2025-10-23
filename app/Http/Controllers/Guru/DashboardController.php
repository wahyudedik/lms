<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = auth()->user();

        // Get guru's courses
        $courses = $guru->teachingCourses()->with('enrollments')->get();

        // Statistics
        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum(fn($c) => $c->enrollments->count()),
            'total_exams' => $guru->teachingCourses()->withCount('exams')->get()->sum('exams_count'),
            'pending_essays' => ExamAttempt::whereHas('exam', function ($q) use ($guru) {
                $q->whereIn('course_id', $guru->teachingCourses->pluck('id'));
            })
                ->where('status', 'pending_manual_grading')
                ->count(),
        ];

        // Recent activities
        $recentAttempts = ExamAttempt::with(['user', 'exam'])
            ->whereHas('exam', function ($q) use ($guru) {
                $q->whereIn('course_id', $guru->teachingCourses->pluck('id'));
            })
            ->where('status', 'graded')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Upcoming exams
        $upcomingExams = \App\Models\Exam::whereIn('course_id', $courses->pluck('id'))
            ->where('is_published', true)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Recent courses
        $recentCourses = $courses->sortByDesc('updated_at')->take(3);

        return view('guru.dashboard', compact('stats', 'recentAttempts', 'upcomingExams', 'recentCourses'));
    }
}
