<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamAttemptController extends Controller
{
    /**
     * Start a new exam attempt
     */
    public function start(Exam $exam)
    {
        // Check if student is enrolled in the course
        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $exam->course_id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(403, 'You must be enrolled in this course to take this exam.');
        }

        // Check if exam is active
        if (!$exam->isActive()) {
            return back()->with('error', 'This exam is not currently available.');
        }

        // Check if user can take the exam
        if (!$exam->canUserTake(auth()->id())) {
            return back()->with('error', 'You have reached the maximum number of attempts for this exam.');
        }

        // ✅ FIX BUG #2 & #4: Use transaction with lock to prevent race condition
        return DB::transaction(function () use ($exam) {
            // Lock the rows to prevent race condition
            $inProgressAttempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', auth()->id())
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->first();

            if ($inProgressAttempt) {
                return redirect()->route('siswa.exams.take', $inProgressAttempt);
            }

            // Create new attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => auth()->id(),
            ]);

            $attempt->start();

            // Create placeholder answers for all questions (all or nothing)
            $questions = $exam->questions;
            foreach ($questions as $question) {
                $answerData = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'answer' => null,
                ];

                // Store shuffled options if exam has shuffle_options enabled
                if ($exam->shuffle_options && ($question->type === 'mcq_single' || $question->type === 'mcq_multiple')) {
                    $answerData['shuffled_options'] = $question->getShuffledOptions();
                }

                Answer::create($answerData);
            }

            return redirect()->route('siswa.exams.take', $attempt);
        });
    }

    /**
     * Display the exam taking interface
     */
    public function take(ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('siswa.exams.review-attempt', $attempt);
        }

        // Check if time is up
        if ($attempt->isTimeUp()) {
            $attempt->submit();
            return redirect()
                ->route('siswa.exams.review-attempt', $attempt)
                ->with('info', 'Time is up! Your exam has been automatically submitted.');
        }

        $exam = $attempt->exam;
        $questions = $attempt->getOrderedQuestions();
        $answers = $attempt->answers()->with('question')->get();

        return view('siswa.exams.take', compact('attempt', 'exam', 'questions', 'answers'));
    }

    /**
     * Save an answer during the exam
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Exam already submitted'], 400);
        }

        // ✅ FIX BUG #5: Server-side time validation (cannot be bypassed)
        if ($attempt->isTimeUp()) {
            // Force submit if time is up
            if ($attempt->status === 'in_progress') {
                $attempt->submit();
            }
            return response()->json([
                'success' => false,
                'message' => 'Time is up! Exam has been automatically submitted.',
                'timeUp' => true,
            ], 400);
        }

        // Additional: Validate time hasn't exceeded (safety check)
        $timeRemaining = $attempt->getTimeRemaining();
        if ($timeRemaining <= 0) {
            $attempt->submit();
            return response()->json([
                'success' => false,
                'message' => 'Time limit exceeded.',
                'timeUp' => true,
            ], 400);
        }

        // ✅ FIX BUG #3: Validate question belongs to this exam
        $validated = $request->validate([
            'question_id' => [
                'required',
                Rule::exists('questions', 'id')->where('exam_id', $attempt->exam_id),
            ],
            'answer' => 'nullable',
        ]);

        $answer = Answer::where('attempt_id', $attempt->id)
            ->where('question_id', $validated['question_id'])
            ->firstOrFail();

        $answer->update(['answer' => $validated['answer']]);

        return response()->json(['success' => true, 'message' => 'Answer saved']);
    }

    /**
     * Submit the exam
     */
    public function submit(ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // ✅ FIX BUG #8: Atomic update to prevent double submission
        $updated = ExamAttempt::where('id', $attempt->id)
            ->where('status', 'in_progress')
            ->update([
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);

        if (!$updated) {
            // Already submitted or not in progress
            return redirect()->route('siswa.exams.review-attempt', $attempt)
                ->with('info', 'Exam has already been submitted.');
        }

        // Reload and auto-grade
        $attempt->refresh();
        
        // Calculate time spent
        if ($attempt->started_at) {
            $attempt->time_spent = $attempt->started_at->diffInSeconds($attempt->submitted_at);
            $attempt->save();
        }
        
        $attempt->autoGrade();

        return redirect()
            ->route('siswa.exams.review-attempt', $attempt)
            ->with('success', 'Ujian berhasil dikumpulkan!');
    }

    /**
     * Track tab switch violation
     */
    public function trackTabSwitch(Request $request, ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false], 400);
        }

        $attempt->recordTabSwitch();

        // Check if attempt was auto-submitted due to max violations
        if ($attempt->fresh()->status !== 'in_progress') {
            return response()->json([
                'success' => true,
                'autoSubmitted' => true,
                'message' => 'Maximum tab switches exceeded. Exam auto-submitted.',
            ]);
        }

        return response()->json([
            'success' => true,
            'tabSwitches' => $attempt->tab_switches,
            'maxTabSwitches' => $attempt->exam->max_tab_switches,
        ]);
    }

    /**
     * Track fullscreen exit violation
     */
    public function trackFullscreenExit(Request $request, ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }

        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false], 400);
        }

        $attempt->recordFullscreenExit();

        return response()->json([
            'success' => true,
            'fullscreenExits' => $attempt->fullscreen_exits,
        ]);
    }

    /**
     * Get time remaining for an attempt
     */
    public function getTimeRemaining(ExamAttempt $attempt)
    {
        // Check if attempt belongs to this user
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }

        return response()->json([
            'success' => true,
            'timeRemaining' => $attempt->getTimeRemaining(),
            'isTimeUp' => $attempt->isTimeUp(),
        ]);
    }
}
