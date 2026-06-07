<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams
     */
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'creator']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('title', 'like', '%' . $search . '%');
        }

        $exams = $query->latest()->paginate(15);
        $courses = Course::select('id', 'title')->orderBy('title')->get();

        return view('admin.exams.index', compact('exams', 'courses'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $courses = Course::select('id', 'title')->orderBy('title')->get();
        return view('admin.exams.create', compact('courses'));
    }

    /**
     * Store a newly created exam in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'pass_score' => 'required|numeric|min:0|max:100',
            'require_fullscreen' => 'boolean',
            'detect_tab_switch' => 'boolean',
            'max_tab_switches' => 'required_if:detect_tab_switch,true|integer|min:1',
            'is_published' => 'boolean',
            'allow_token_access' => 'boolean',
            'require_guest_name' => 'boolean',
            'require_guest_email' => 'boolean',
            'max_token_uses' => 'nullable|integer|min:1',
            'offline_enabled' => 'boolean',
            'offline_cache_duration' => 'nullable|integer|min:1|max:168',
        ]);

        // Explicitly handle boolean checkboxes
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_results_immediately'] = $request->boolean('show_results_immediately');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen');
        $validated['detect_tab_switch'] = $request->boolean('detect_tab_switch');
        $validated['allow_token_access'] = $request->boolean('allow_token_access');
        $validated['require_guest_name'] = $request->boolean('require_guest_name');
        $validated['require_guest_email'] = $request->boolean('require_guest_email');

        $validated['created_by'] = auth()->id();
        $validated['start_time'] = $this->normalizeDateTime($request->input('start_time'));
        $validated['end_time'] = $this->normalizeDateTime($request->input('end_time'));
        $validated['offline_enabled'] = $request->boolean('offline_enabled');
        $validated['offline_cache_duration'] = $validated['offline_enabled']
            ? ($request->input('offline_cache_duration') ?: 24)
            : 24;

        if ($request->boolean('is_published')) {
            $validated['is_published'] = true;
            $validated['published_at'] = now();
        } else {
            $validated['is_published'] = false;
            $validated['published_at'] = null;
        }

        $exam = Exam::create($validated);

        // Generate token if token access is enabled
        if ($request->has('allow_token_access') && $request->allow_token_access) {
            $exam->generateAccessToken();
        }

        // Notify enrolled students if exam is published
        if ($exam->is_published) {
            $students = $exam->course->enrollments()
                ->where('status', 'active')
                ->whereHas('user', fn ($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            foreach ($students->chunk(100) as $chunk) {
                \Illuminate\Support\Facades\Notification::send($chunk, new \App\Notifications\ExamScheduled($exam));
            }
        }

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('success', 'Ujian berhasil dibuat!');
    }

    /**
     * Display the specified exam
     */
    public function show(Exam $exam)
    {
        $exam->load(['course', 'creator', 'questions', 'attempts.user']);

        return view('admin.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Exam $exam)
    {
        $courses = Course::select('id', 'title')->orderBy('title')->get();
        return view('admin.exams.edit', compact('exam', 'courses'));
    }

    /**
     * Update the specified exam in storage
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_attempts' => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'pass_score' => 'required|numeric|min:0|max:100',
            'require_fullscreen' => 'boolean',
            'detect_tab_switch' => 'boolean',
            'max_tab_switches' => 'required_if:detect_tab_switch,true|integer|min:1',
            'is_published' => 'boolean',
            'offline_enabled' => 'boolean',
            'offline_cache_duration' => 'nullable|integer|min:1|max:168',
        ]);

        // Explicitly handle boolean checkboxes that might be missing from request when unchecked
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_results_immediately'] = $request->boolean('show_results_immediately');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen');
        $validated['detect_tab_switch'] = $request->boolean('detect_tab_switch');

        $validated['start_time'] = $this->normalizeDateTime($request->input('start_time'));
        $validated['end_time'] = $this->normalizeDateTime($request->input('end_time'));
        $validated['offline_enabled'] = $request->boolean('offline_enabled');
        $validated['offline_cache_duration'] = $validated['offline_enabled']
            ? ($request->input('offline_cache_duration') ?: 24)
            : 24;

        if ($request->boolean('is_published')) {
            $validated['is_published'] = true;
            if (!$exam->is_published) {
                $validated['published_at'] = now();
            }
        } else {
            $validated['is_published'] = false;
            $validated['published_at'] = null;
        }

        $oldPassScore = $exam->pass_score;
        $exam->update($validated);

        if ($oldPassScore != $exam->pass_score) {
            $exam->attempts()
                ->whereNotNull('score')
                ->update([
                    'passed' => \Illuminate\Support\Facades\DB::raw("CASE WHEN score >= {$exam->pass_score} THEN 1 ELSE 0 END")
                ]);
        }

        return redirect()
            ->route('admin.exams.show', $exam)
            ->with('success', 'Ujian berhasil diperbarui!');
    }

    /**
     * Remove the specified exam from storage
     */
    public function destroy(Exam $exam)
    {
        $courseId = $exam->course_id;
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Ujian berhasil dihapus!');
    }

    /**
     * Toggle exam publish status
     */
    public function toggleStatus(Exam $exam)
    {
        $wasPublished = $exam->is_published;

        $exam->update([
            'is_published' => !$exam->is_published,
            'published_at' => !$wasPublished ? now() : null,
        ]);

        // Notify enrolled students if exam just became published
        if (!$wasPublished && $exam->is_published) {
            $students = $exam->course->enrollments()
                ->where('status', 'active')
                ->whereHas('user', fn ($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            foreach ($students->chunk(100) as $chunk) {
                \Illuminate\Support\Facades\Notification::send($chunk, new \App\Notifications\ExamScheduled($exam));
            }
        }

        $status = $exam->is_published ? 'dipublikasikan' : 'disembunyikan';

        return back()->with('success', "Ujian berhasil {$status}!");
    }

    /**
     * Duplicate an exam
     */
    public function duplicate(Exam $exam)
    {
        $newExam = $exam->replicate();
        $newExam->title = $exam->title . ' (Copy)';
        $newExam->is_published = false;
        $newExam->published_at = null;
        $newExam->current_token_uses = 0;
        $newExam->created_by = auth()->id();

        // Clear token before saving to avoid unique constraint violation;
        // a new unique token will be generated after save if needed.
        if ($exam->allow_token_access) {
            $newExam->access_token = null;
        }

        $newExam->save();

        if ($exam->allow_token_access) {
            $newExam->generateAccessToken();
        }

        // Duplicate questions
        foreach ($exam->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $newExam->id;
            $newQuestion->save();
        }

        return redirect()
            ->route('admin.exams.show', $newExam)
            ->with('success', 'Ujian berhasil diduplikasi!');
    }

    /**
     * View exam results/statistics
     */
    public function results(Request $request, Exam $exam)
    {
        $exam->load(['attempts.user', 'questions']);

        $classId = $request->query('class_id');

        // Fetch classes relevant to the exam's course
        $classes = \App\Models\SchoolClass::whereHas('users', function ($q) use ($exam) {
            $q->whereHas('enrollments', fn($e) => $e->where('course_id', $exam->course_id)->where('status', 'active'));
        })->orderBy('name')->get();

        // Use SQL aggregates instead of loading all records into memory
        $attemptsQuery = $exam->attempts()->where('status', 'graded');
        if ($classId) {
            $attemptsQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
        }

        $completedCount = (clone $attemptsQuery)->count();

        if ($completedCount > 0) {
            $aggregates = (clone $attemptsQuery)
                ->selectRaw('AVG(score) as avg_score, MAX(score) as max_score, MIN(score) as min_score, SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_count')
                ->first();

            $totalAttemptsQuery = $exam->attempts();
            if ($classId) {
                $totalAttemptsQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
            }

            $statistics = [
                'total_attempts' => $totalAttemptsQuery->count(),
                'completed_attempts' => $completedCount,
                'average_score' => round($aggregates->avg_score, 2),
                'highest_score' => round($aggregates->max_score, 2),
                'lowest_score' => round($aggregates->min_score, 2),
                'pass_rate' => round($aggregates->passed_count / $completedCount * 100, 2),
            ];
        } else {
            $totalAttemptsQuery = $exam->attempts();
            if ($classId) {
                $totalAttemptsQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
            }

            $statistics = [
                'total_attempts' => $totalAttemptsQuery->count(),
                'completed_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
            ];
        }

        $activeTab = $request->query('tab', 'attempts');

        // Get IDs of students who have attempted this exam, filtered by class if applicable
        $attemptedUserIdsQuery = $exam->attempts()->whereNotNull('user_id');
        if ($classId) {
            $attemptedUserIdsQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
        }
        $attemptedUserIds = $attemptedUserIdsQuery->pluck('user_id')->unique();

        // Count enrolled students who have NOT attempted, filtered by class if applicable
        $unattemptedQuery = \App\Models\User::whereIn('role', ['siswa', 'mahasiswa'])
            ->whereHas('enrollments', fn ($q) => $q->where('course_id', $exam->course_id)->where('status', 'active'));

        if ($classId) {
            $unattemptedQuery->where('school_class_id', $classId);
        }

        $unattemptedCount = (clone $unattemptedQuery)->whereNotIn('id', $attemptedUserIds)->count();

        $attempts = null;
        $unattemptedUsers = null;

        if ($activeTab === 'unattempted') {
            $unattemptedUsers = $unattemptedQuery
                ->whereNotIn('id', $attemptedUserIds)
                ->with('schoolClass:id,name')
                ->select('id', 'name', 'email', 'gender', 'profile_photo', 'school_class_id')
                ->paginate(20)
                ->withQueryString();
        } else {
            $attemptsQuery = $exam->attempts()
                ->where('status', '!=', 'in_progress');

            if ($classId) {
                $attemptsQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
            }

            if ($request->query('filter') === 'best') {
                $userBestQuery = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                    ->where('status', '!=', 'in_progress')
                    ->whereNotNull('user_id');

                if ($classId) {
                    $userBestQuery->whereHas('user', fn($q) => $q->where('school_class_id', $classId));
                }

                $userBestIds = $userBestQuery
                    ->select('id', 'user_id', 'score')
                    ->get()
                    ->groupBy('user_id')
                    ->map(fn($group) => $group->sortByDesc('score')->first()->id)
                    ->values()
                    ->toArray();

                $guestBestIds = [];
                if (!$classId) {
                    $guestBestIds = \App\Models\ExamAttempt::where('exam_id', $exam->id)
                        ->where('status', '!=', 'in_progress')
                        ->whereNull('user_id')
                        ->select('id', 'guest_email', 'score')
                        ->get()
                        ->groupBy('guest_email')
                        ->map(fn($group) => $group->sortByDesc('score')->first()->id)
                        ->values()
                        ->toArray();
                }

                $bestAttemptIds = array_merge($userBestIds, $guestBestIds);
                $attemptsQuery->whereIn('id', $bestAttemptIds);
            }

            $attempts = $attemptsQuery
                ->with('user.schoolClass')
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.exams.results', compact('exam', 'statistics', 'attempts', 'unattemptedUsers', 'activeTab', 'unattemptedCount', 'classes'));
    }

    /**
     * Normalize datetime input using application timezone before persisting.
     */
    protected function normalizeDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $appTimezone = config('app.timezone', 'UTC');

        return Carbon::parse($value, $appTimezone)->timezone('UTC');
    }
}
