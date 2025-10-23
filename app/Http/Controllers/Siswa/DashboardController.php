<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get enrolled courses
        $enrolledCourses = $student->enrolledCourses()
            ->where('enrollments.status', 'active')
            ->withPivot('progress')
            ->get();

        // Get exam attempts
        $attempts = ExamAttempt::where('user_id', $student->id)
            ->where('status', 'graded')
            ->get();

        // Statistics
        $stats = [
            'enrolled_courses' => $enrolledCourses->count(),
            'completed_courses' => $enrolledCourses->filter(fn($c) => $c->pivot->progress >= 100)->count(),
            'pending_exams' => Exam::whereHas('course', function ($q) use ($student) {
                $q->whereHas('enrollments', function ($q2) use ($student) {
                    $q2->where('user_id', $student->id)->where('status', 'active');
                });
            })
                ->where('is_published', true)
                ->whereDoesntHave('attempts', function ($q) use ($student) {
                    $q->where('user_id', $student->id);
                })
                ->count(),
            'avg_score' => $attempts->avg('score') ?? 0,
        ];

        // Recent grades
        $recentGrades = ExamAttempt::with('exam.course')
            ->where('user_id', $student->id)
            ->where('status', 'graded')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Upcoming exams
        $upcomingExams = Exam::whereHas('course', function ($q) use ($student) {
            $q->whereHas('enrollments', function ($q2) use ($student) {
                $q2->where('user_id', $student->id)->where('status', 'active');
            });
        })
            ->where('is_published', true)
            ->where(function ($q) {
                $q->where('start_time', '>', now())
                    ->orWhere(function ($q2) {
                        $q2->where('start_time', '<=', now())
                            ->where(function ($q3) {
                                $q3->whereNull('end_time')
                                    ->orWhere('end_time', '>=', now());
                            });
                    });
            })
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // My courses (recent 3)
        $myCourses = $enrolledCourses->sortByDesc('pivot.updated_at')->take(3);

        return view('siswa.dashboard', compact('stats', 'recentGrades', 'upcomingExams', 'myCourses'));
    }
}
