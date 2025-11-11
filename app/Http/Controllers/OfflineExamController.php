<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfflineExamController extends Controller
{
    /**
     * Show offline exam list
     */
    public function index()
    {
        $user = Auth::user();

        // Get available exams for offline mode
        $exams = Exam::where('is_published', true)
            ->where('offline_enabled', true)
            ->with(['course', 'questions'])
            ->get()
            ->map(function ($exam) use ($user) {
                $attempt = ExamAttempt::where('exam_id', $exam->id)
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();

                $exam->is_cached = false; // Will be updated by JS
                $exam->last_attempt = $attempt;

                return $exam;
            });

        return view('offline.exams.index', compact('exams'));
    }

    /**
     * Show specific exam for offline mode
     */
    public function show(Exam $exam)
    {
        // Check if exam is available for offline mode
        if (!$exam->offline_enabled) {
            abort(403, 'This exam is not available for offline mode');
        }

        $user = Auth::user();

        // Check if user already has an active attempt
        $activeAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->whereNull('finished_at')
            ->first();

        if (!$activeAttempt) {
            // Create new attempt
            $activeAttempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'is_offline' => true,
            ]);
        }

        // Load exam with questions
        $exam->load(['questions' => function ($query) {
            $query->orderBy('order');
        }]);

        // Get existing answers
        $existingAnswers = Answer::where('attempt_id', $activeAttempt->id)
            ->pluck('answer_text', 'question_id')
            ->toArray();

        return view('offline.exams.take', compact('exam', 'activeAttempt', 'existingAnswers'));
    }

    /**
     * Get exam data as JSON for offline caching
     */
    public function getExamData(Exam $exam)
    {
        if (!$exam->offline_enabled) {
            abort(403, 'This exam is not available for offline mode');
        }

        $exam->load(['questions' => function ($query) {
            $query->orderBy('order');
        }, 'course']);

        return response()->json([
            'exam' => $exam,
            'can_cache' => true,
            'cached_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Save answer (works offline)
     */
    public function saveAnswer(Request $request, Exam $exam)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable|string',
        ]);

        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        // Verify ownership
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        // Save or update answer
        Answer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer,
                'saved_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Answer saved',
            'saved_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Submit exam (can be queued when offline)
     */
    public function submit(Request $request, Exam $exam)
    {
        $request->validate([
            'attempt_id' => 'required|exists:exam_attempts,id',
            'answers' => 'required|array',
        ]);

        $attempt = ExamAttempt::findOrFail($request->attempt_id);

        // Verify ownership
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            // Save all answers
            foreach ($request->answers as $questionId => $answer) {
                Answer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'answer_text' => $answer,
                        'saved_at' => now(),
                    ]
                );
            }

            // Mark attempt as finished
            $attempt->update([
                'finished_at' => now(),
                'submitted_at' => now(),
                'is_offline' => $request->boolean('was_offline', false),
            ]);

            // Calculate score
            $this->calculateScore($attempt);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'attempt_id' => $attempt->id,
                'score' => $attempt->score,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit exam: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate exam score
     */
    private function calculateScore(ExamAttempt $attempt)
    {
        $questions = Question::where('exam_id', $attempt->exam_id)->get();
        $answers = Answer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

        $totalQuestions = $questions->count();
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $userAnswer = $answers->get($question->id);

            if (!$userAnswer) {
                continue;
            }

            // Check if answer is correct
            if ($this->isAnswerCorrect($question, $userAnswer->answer_text)) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

        $attempt->update([
            'score' => round($score, 2),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ]);
    }

    /**
     * Check if answer is correct
     */
    private function isAnswerCorrect(Question $question, $userAnswer)
    {
        if ($question->type === 'multiple_choice') {
            return $userAnswer === $question->correct_answer;
        }

        if ($question->type === 'true_false') {
            return strtolower($userAnswer) === strtolower($question->correct_answer);
        }

        if ($question->type === 'short_answer') {
            return strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
        }

        // Essay questions need manual grading
        return false;
    }

    /**
     * Get sync status
     */
    public function getSyncStatus(Request $request)
    {
        $user = Auth::user();

        // Get pending submissions
        $pendingAttempts = ExamAttempt::where('user_id', $user->id)
            ->where('is_offline', true)
            ->whereNull('submitted_at')
            ->count();

        return response()->json([
            'pending_submissions' => $pendingAttempts,
            'last_sync' => now()->toIso8601String(),
        ]);
    }
}
