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
            $query->where('title', 'like', '%' . $request->search . '%');
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

        $exam->update($validated);

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
    public function results(Exam $exam)
    {
        $exam->load(['attempts.user', 'questions']);

        // Get graded attempts for statistics (single query)
        $gradedAttempts = $exam->attempts()
            ->where('status', 'graded')
            ->select('score', 'passed')
            ->get();

        $completedCount = $gradedAttempts->count();

        $statistics = [
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $completedCount,
            'average_score' => $completedCount > 0 ? $gradedAttempts->avg('score') : 0,
            'highest_score' => $completedCount > 0 ? $gradedAttempts->max('score') : 0,
            'lowest_score' => $completedCount > 0 ? $gradedAttempts->min('score') : 0,
            'pass_rate' => $completedCount > 0 ? ($gradedAttempts->where('passed', true)->count() / $completedCount * 100) : 0,
        ];

        $attempts = $exam->attempts()
            ->with('user:id,name,email')
            ->where('status', '!=', 'in_progress')
            ->latest()
            ->paginate(20);

        return view('admin.exams.results', compact('exam', 'statistics', 'attempts'));
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
