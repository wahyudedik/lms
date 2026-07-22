<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Constants\AuthorizationMessages;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use ResolvesRolePrefix;
    /**
     * Display a listing of the exams for guru's courses
     */
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'creator'])
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            });

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
        $courses = auth()->user()->teachingCourses;

        return view('guru.exams.index', compact('exams', 'courses'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $courses = auth()->user()->teachingCourses;
        return view('guru.exams.create', compact('courses'));
    }

    /**
     * Store a newly created exam in storage
     */
    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

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
            'detect_window_blur' => 'boolean',
            'max_window_blurs' => 'required_if:detect_window_blur,true|integer|min:1',
            'detect_multiple_screens' => 'boolean',
            'detect_inactivity' => 'boolean',
            'max_inactivity_minutes' => 'required_if:detect_inactivity,true|integer|min:1',
            'block_keys_and_copy' => 'boolean',
            'is_published' => 'boolean',
        ]);

        // Explicitly handle boolean checkboxes
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_results_immediately'] = $request->boolean('show_results_immediately');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen');
        $validated['detect_tab_switch'] = $request->boolean('detect_tab_switch');
        $validated['detect_window_blur'] = $request->boolean('detect_window_blur');
        $validated['detect_multiple_screens'] = $request->boolean('detect_multiple_screens');
        $validated['detect_inactivity'] = $request->boolean('detect_inactivity');
        $validated['block_keys_and_copy'] = $request->boolean('block_keys_and_copy');

        if (!$validated['detect_window_blur']) {
            $validated['max_window_blurs'] = 3;
        }
        if (!$validated['detect_inactivity']) {
            $validated['max_inactivity_minutes'] = 3;
        }

        // Check if course belongs to this guru
        $course = \App\Models\Course::findOrFail($validated['course_id']);
        $this->authorize('update', $course);

        $validated['created_by'] = auth()->id();

        if ($request->has('is_published') && $request->is_published) {
            $validated['published_at'] = now();
        }

        $exam = Exam::create($validated);

        // Notify enrolled students if exam is published
        if ($exam->is_published) {
            $students = $exam->course->enrollments()
                ->where('status', 'active')
                ->whereHas('user', fn($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter();

            foreach ($students->chunk(100) as $chunk) {
                \Illuminate\Support\Facades\Notification::send($chunk, new \App\Notifications\ExamScheduled($exam));
            }
        }

        return redirect()
            ->to($this->teacherRoute('exams.show', $exam))
            ->with('success', 'Ujian berhasil dibuat!');
    }

    /**
     * Display the specified exam
     */
    public function show(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('view', $exam);

        $exam->load(['course', 'creator', 'questions', 'attempts.user']);

        return view('guru.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('update', $exam);

        $courses = auth()->user()->teachingCourses;
        return view('guru.exams.edit', compact('exam', 'courses'));
    }

    /**
     * Update the specified exam in storage
     */
    public function update(Request $request, Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('update', $exam);

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
            'detect_window_blur' => 'boolean',
            'max_window_blurs' => 'required_if:detect_window_blur,true|integer|min:1',
            'detect_multiple_screens' => 'boolean',
            'detect_inactivity' => 'boolean',
            'max_inactivity_minutes' => 'required_if:detect_inactivity,true|integer|min:1',
            'block_keys_and_copy' => 'boolean',
            'is_published' => 'boolean',
        ]);

        // Explicitly handle boolean checkboxes that might be missing from request when unchecked
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_options'] = $request->boolean('shuffle_options');
        $validated['show_results_immediately'] = $request->boolean('show_results_immediately');
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen');
        $validated['detect_tab_switch'] = $request->boolean('detect_tab_switch');
        $validated['detect_window_blur'] = $request->boolean('detect_window_blur');
        $validated['detect_multiple_screens'] = $request->boolean('detect_multiple_screens');
        $validated['detect_inactivity'] = $request->boolean('detect_inactivity');
        $validated['block_keys_and_copy'] = $request->boolean('block_keys_and_copy');

        if (!$validated['detect_window_blur']) {
            $validated['max_window_blurs'] = 3;
        }
        if (!$validated['detect_inactivity']) {
            $validated['max_inactivity_minutes'] = 3;
        }

        // Check if new course belongs to this guru using policy
        $course = \App\Models\Course::findOrFail($validated['course_id']);
        $this->authorize('update', $course);

        if ($request->has('is_published') && $request->is_published && !$exam->is_published) {
            $validated['published_at'] = now();
        } elseif (!$request->has('is_published') || !$request->is_published) {
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
            ->to($this->teacherRoute('exams.show', $exam))
            ->with('success', 'Ujian berhasil diperbarui!');
    }

    /**
     * Remove the specified exam from storage
     */
    public function destroy(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('delete', $exam);

        $exam->delete();

        return redirect()
            ->to($this->teacherRoute('exams.index'))
            ->with('success', 'Ujian berhasil dihapus!');
    }

    /**
     * Toggle exam publish status
     */
    public function toggleStatus(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('update', $exam);

        $wasPublished = $exam->is_published;

        $exam->update([
            'is_published' => !$exam->is_published,
            'published_at' => !$wasPublished ? now() : null,
        ]);

        // Notify enrolled students if exam just became published
        if (!$wasPublished && $exam->is_published) {
            $students = $exam->course->enrollments()
                ->where('status', 'active')
                ->whereHas('user', fn($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
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
        // Check authorization using policy - need to view original and create new
        $this->authorize('view', $exam);
        $this->authorize('create', Exam::class);

        $newExam = $exam->replicate();
        $newExam->title = $exam->title . ' (Copy)';
        $newExam->is_published = false;
        $newExam->published_at = null;
        $newExam->created_by = auth()->id();
        $newExam->save();

        // Duplicate questions
        foreach ($exam->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $newExam->id;
            $newQuestion->save();
        }

        return redirect()
            ->to($this->teacherRoute('exams.show', $newExam))
            ->with('success', 'Ujian berhasil diduplikasi!');
    }

    /**
     * View exam results/statistics
     */
    public function results(Request $request, Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('view', $exam);

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

        return view('guru.exams.results', compact('exam', 'statistics', 'attempts', 'unattemptedUsers', 'activeTab', 'unattemptedCount', 'classes'));
    }

    /**
     * Show essay grading interface
     */
    public function reviewEssays(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('view', $exam);

        // Get all attempts that have essay questions
        $attempts = $exam->attempts()
            ->with(['user', 'answers.question'])
            ->where('status', '!=', 'in_progress')
            ->latest()
            ->get();

        // Filter attempts that have essay answers
        $attemptsWithEssays = $attempts->filter(function ($attempt) {
            return $attempt->answers->filter(function ($answer) {
                return $answer->question->type === 'essay';
            })->isNotEmpty();
        });

        // Get essay questions from this exam
        $essayQuestions = $exam->questions()->where('type', 'essay')->get();

        return view('guru.exams.review-essays', compact('exam', 'attemptsWithEssays', 'essayQuestions'));
    }

    /**
     * Grade essay answers
     */
    public function gradeEssay(Request $request, Exam $exam, \App\Models\Answer $answer)
    {
        // Check authorization using policy - grading is an update operation
        $this->authorize('update', $exam);

        // Bug #16: IDOR prevention - verify answer belongs to this exam
        if ($answer->question->exam_id !== $exam->id) {
            abort(403, 'Jawaban ini bukan milik ujian ini.');
        }

        $validated = $request->validate([
            'points_earned' => 'required|numeric|min:0|max:' . $answer->question->points,
            'feedback' => 'nullable|string',
        ]);

        $answer->update([
            'points_earned' => $validated['points_earned'],
            'feedback' => $validated['feedback'],
            'is_correct' => $validated['points_earned'] == $answer->question->points,
        ]);

        // Recalculate attempt score
        $attempt = $answer->attempt;
        $attempt->finalizeGrading();

        return back()->with('success', 'Jawaban essay berhasil dinilai!');
    }
}
