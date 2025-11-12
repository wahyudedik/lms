<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Constants\AuthorizationMessages;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display available exams for enrolled courses
     */
    public function index(Request $request)
    {
        // Get courses the student is enrolled in
        $enrolledCourseIds = auth()->user()->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $query = Exam::with(['course', 'creator'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->published()
            ->active();

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams = $query->latest()->paginate(12);

        // Get courses for filter
        $enrolledCourses = auth()->user()->enrolledCourses()
            ->where('enrollments.status', 'active')
            ->get();

        return view('siswa.exams.index', compact('exams', 'enrolledCourses'));
    }

    /**
     * Display exam details and allow student to start
     */
    public function show(Exam $exam)
    {
        // Check if student is enrolled in the course
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, AuthorizationMessages::EXAM_ENROLLMENT_REQUIRED);
        }

        // Check if exam is active
        if (!$exam->isActive()) {
            abort(403, AuthorizationMessages::EXAM_NOT_AVAILABLE);
        }

        // Get user's attempts
        $attempts = $exam->getUserAttempts(auth()->id());
        $canTake = $exam->canUserTake(auth()->id());
        $remainingAttempts = $exam->getRemainingAttempts(auth()->id());

        // Check if there's an in-progress attempt
        $inProgressAttempt = $attempts->where('status', 'in_progress')->first();

        return view('siswa.exams.show', compact(
            'exam',
            'attempts',
            'canTake',
            'remainingAttempts',
            'inProgressAttempt'
        ));
    }

    /**
     * Display my exam attempts history
     */
    public function myAttempts()
    {
        $attempts = ExamAttempt::with(['exam.course'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'in_progress')
            ->latest()
            ->paginate(15);

        return view('siswa.exams.my-attempts', compact('attempts'));
    }

    /**
     * Review a completed exam attempt
     */
    public function reviewAttempt(ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            abort(403, AuthorizationMessages::ATTEMPT_ACCESS_DENIED);
        }

        // Check if attempt is completed
        if ($attempt->status === 'in_progress') {
            return redirect()->route('siswa.exams.take', $attempt);
        }

        $exam = $attempt->exam;

        // Check if exam allows showing results
        if (!$exam->show_results_immediately && $attempt->status !== 'graded') {
            abort(403, AuthorizationMessages::RESULTS_NOT_AVAILABLE);
        }

        $attempt->load(['answers.question', 'exam.questions']);

        return view('siswa.exams.review', compact('attempt', 'exam'));
    }
}
