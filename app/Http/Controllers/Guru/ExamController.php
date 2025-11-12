<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Constants\AuthorizationMessages;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
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
            $query->where('title', 'like', '%' . $request->search . '%');
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
            'is_published' => 'boolean',
        ]);

        // Check if course belongs to this guru
        $course = \App\Models\Course::findOrFail($validated['course_id']);
        $this->authorize('update', $course);

        $validated['created_by'] = auth()->id();

        if ($request->has('is_published') && $request->is_published) {
            $validated['published_at'] = now();
        }

        $exam = Exam::create($validated);

        return redirect()
            ->route('guru.exams.show', $exam)
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
            'is_published' => 'boolean',
        ]);

        // Check if new course belongs to this guru using policy
        $course = \App\Models\Course::findOrFail($validated['course_id']);
        $this->authorize('update', $course);

        if ($request->has('is_published') && $request->is_published && !$exam->is_published) {
            $validated['published_at'] = now();
        } elseif (!$request->has('is_published') || !$request->is_published) {
            $validated['published_at'] = null;
        }

        $exam->update($validated);

        return redirect()
            ->route('guru.exams.show', $exam)
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
            ->route('guru.exams.index')
            ->with('success', 'Ujian berhasil dihapus!');
    }

    /**
     * Toggle exam publish status
     */
    public function toggleStatus(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('update', $exam);

        $exam->update([
            'is_published' => !$exam->is_published,
            'published_at' => !$exam->is_published ? now() : null,
        ]);

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
            ->route('guru.exams.show', $newExam)
            ->with('success', 'Ujian berhasil diduplikasi!');
    }

    /**
     * View exam results/statistics
     */
    public function results(Exam $exam)
    {
        // Check authorization using policy
        $this->authorize('view', $exam);

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

        return view('guru.exams.results', compact('exam', 'statistics', 'attempts'));
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
