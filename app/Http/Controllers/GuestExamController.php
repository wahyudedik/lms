<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class GuestExamController extends Controller
{
    /**
     * Show token entry page
     */
    public function index()
    {
        return view('guest.exams.enter-token');
    }

    /**
     * Verify token and show guest information form
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:8',
        ]);

        $token = strtoupper(trim($request->token));
        $exam = Exam::where('access_token', $token)->first();

        if (!$exam) {
            return back()->with('error', 'Token ujian tidak valid!');
        }

        if (!$exam->isTokenAccessAvailable()) {
            if ($exam->max_token_uses && $exam->current_token_uses >= $exam->max_token_uses) {
                return back()->with('error', 'Token ujian sudah mencapai batas penggunaan maksimal!');
            }
            return back()->with('error', 'Ujian tidak tersedia saat ini!');
        }

        // Store token in session
        Session::put('guest_exam_token', $token);
        Session::put('guest_exam_id', $exam->id);

        return redirect()->route('guest.exams.info', $exam->id);
    }

    /**
     * Show guest information form
     */
    public function showInfo($examId)
    {
        // Verify session token
        if (!Session::has('guest_exam_token') || Session::get('guest_exam_id') != $examId) {
            return redirect()->route('guest.exams.index')->with('error', 'Sesi tidak valid!');
        }

        $exam = Exam::findOrFail($examId);

        if (!$exam->isTokenAccessAvailable()) {
            return redirect()->route('guest.exams.index')->with('error', 'Ujian tidak tersedia!');
        }

        return view('guest.exams.info', compact('exam'));
    }

    /**
     * Start guest exam
     */
    public function start(Request $request, $examId)
    {
        // Verify session token
        if (!Session::has('guest_exam_token') || Session::get('guest_exam_id') != $examId) {
            return redirect()->route('guest.exams.index')->with('error', 'Sesi tidak valid!');
        }

        $exam = Exam::with('questions')->findOrFail($examId);

        if (!$exam->isTokenAccessAvailable()) {
            return redirect()->route('guest.exams.index')->with('error', 'Ujian tidak tersedia!');
        }

        // Validate guest information
        $rules = [];
        if ($exam->require_guest_name) {
            $rules['guest_name'] = 'required|string|max:255';
        }
        if ($exam->require_guest_email) {
            $rules['guest_email'] = 'required|email|max:255';
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        // Generate unique guest token for this attempt
        $guestToken = Str::random(32);

        // Create exam attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => null,
            'is_guest' => true,
            'guest_name' => $request->guest_name ?? 'Guest',
            'guest_email' => $request->guest_email ?? null,
            'guest_token' => $guestToken,
            'started_at' => now(),
            'status' => 'in_progress',
            'shuffled_question_ids' => $exam->shuffle_questions
                ? $exam->questions->shuffle()->pluck('id')->toArray()
                : $exam->questions->pluck('id')->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Increment token usage
        $exam->incrementTokenUses();

        // Store attempt token in session
        Session::put('guest_attempt_token', $guestToken);
        Session::put('guest_attempt_id', $attempt->id);

        return redirect()->route('guest.exams.take', $attempt->id);
    }

    /**
     * Show exam taking page for guest
     */
    public function take($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'answers'])->findOrFail($attemptId);

        // Verify session
        if (
            !Session::has('guest_attempt_token') ||
            Session::get('guest_attempt_token') != $attempt->guest_token ||
            Session::get('guest_attempt_id') != $attemptId
        ) {
            abort(403, 'Akses tidak sah!');
        }

        if (!$attempt->is_guest) {
            abort(403, 'Attempt ini bukan untuk guest!');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('guest.exams.review', $attempt->id)
                ->with('info', 'Ujian sudah selesai!');
        }

        // Check if time limit exceeded
        if ($attempt->hasTimeExpired()) {
            $attempt->autoSubmit();
            return redirect()->route('guest.exams.review', $attempt->id)
                ->with('warning', 'Waktu ujian telah habis! Jawaban Anda telah disimpan secara otomatis.');
        }

        $exam = $attempt->exam;
        $questions = $exam->questions;

        // Shuffle if needed
        if ($exam->shuffle_questions && !empty($attempt->shuffled_question_ids)) {
            $questions = $questions->sortBy(function ($question) use ($attempt) {
                return array_search($question->id, $attempt->shuffled_question_ids);
            });
        } else {
            $questions = $questions->sortBy('order');
        }

        return view('guest.exams.take', compact('attempt', 'exam', 'questions'));
    }

    /**
     * Save answer for guest (AJAX)
     */
    public function saveAnswer(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::with('exam')->findOrFail($attemptId);

        // Verify session
        if (
            !Session::has('guest_attempt_token') ||
            Session::get('guest_attempt_token') != $attempt->guest_token
        ) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$attempt->is_guest || $attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 400);
        }

        // Validate question belongs to this exam
        $request->validate([
            'question_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('questions', 'id')->where('exam_id', $attempt->exam_id),
            ],
            'answer' => 'nullable',
        ]);

        // Check if time is up (server-side validation)
        if ($attempt->hasTimeExpired()) {
            $attempt->autoSubmit();
            return response()->json([
                'success' => false,
                'message' => 'Waktu ujian telah habis!',
                'timeUp' => true,
            ], 400);
        }

        $answer = Answer::updateOrCreate(
            [
                'attempt_id' => $attempt->id, // ✅ Fixed: Changed from exam_attempt_id to attempt_id
                'question_id' => $request->question_id,
            ],
            [
                'answer' => $request->answer,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
        ]);
    }

    /**
     * Submit exam for guest
     */
    public function submit(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::with('exam.questions')->findOrFail($attemptId);

        // Verify session
        if (
            !Session::has('guest_attempt_token') ||
            Session::get('guest_attempt_token') != $attempt->guest_token
        ) {
            abort(403, 'Akses tidak sah!');
        }

        if (!$attempt->is_guest || $attempt->status !== 'in_progress') {
            return redirect()->route('guest.exams.review', $attempt->id)
                ->with('info', 'Ujian sudah selesai!');
        }

        // Use submit() method which handles status correctly
        // ✅ Fixed: Changed from status 'completed' to use submit() method
        $attempt->submit();

        return redirect()->route('guest.exams.review', $attempt->id)
            ->with('success', 'Ujian berhasil diselesaikan!');
    }

    /**
     * Show exam results for guest
     */
    public function review($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'answers.question'])->findOrFail($attemptId);

        // Verify session
        if (
            !Session::has('guest_attempt_token') ||
            Session::get('guest_attempt_token') != $attempt->guest_token
        ) {
            abort(403, 'Akses tidak sah!');
        }

        if (!$attempt->is_guest) {
            abort(403, 'Attempt ini bukan untuk guest!');
        }

        $exam = $attempt->exam;

        // Get questions in order
        $questions = $exam->questions;
        if ($exam->shuffle_questions && !empty($attempt->shuffled_question_ids)) {
            $questions = $questions->sortBy(function ($question) use ($attempt) {
                return array_search($question->id, $attempt->shuffled_question_ids);
            });
        } else {
            $questions = $questions->sortBy('order');
        }

        return view('guest.exams.review', compact('attempt', 'exam', 'questions'));
    }

    public function reviewByToken($token)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'answers.question'])
            ->where('guest_token', $token)
            ->where('is_guest', true)
            ->firstOrFail();

        if ($attempt->status === 'in_progress') {
            return redirect()->route('guest.exams.index')
                ->with('error', __('Guest exam results are only available after completion.'));
        }

        $exam = $attempt->exam;

        $questions = $exam->questions;
        if ($exam->shuffle_questions && !empty($attempt->shuffled_question_ids)) {
            $questions = $questions->sortBy(function ($question) use ($attempt) {
                return array_search($question->id, $attempt->shuffled_question_ids);
            });
        } else {
            $questions = $questions->sortBy('order');
        }

        return view('guest.exams.review', compact('attempt', 'exam', 'questions'));
    }

    /**
     * Log anti-cheat violation for guest (AJAX)
     */
    public function logViolation(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);

        // Verify session
        if (
            !Session::has('guest_attempt_token') ||
            Session::get('guest_attempt_token') != $attempt->guest_token
        ) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$attempt->is_guest || $attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 400);
        }

        $request->validate([
            'type' => 'required|in:tab_switch,fullscreen_exit',
        ]);

        $violations = $attempt->violations ?? [];
        $violations[] = [
            'type' => $request->type,
            'time' => now()->toISOString(),
        ];

        if ($request->type === 'tab_switch') {
            $attempt->tab_switches++;
        } elseif ($request->type === 'fullscreen_exit') {
            $attempt->fullscreen_exits++;
        }

        $attempt->violations = $violations;
        $attempt->save();

        // Check if max violations reached
        $exam = $attempt->exam;
        if ($exam->detect_tab_switch && $attempt->tab_switches >= $exam->max_tab_switches) {
            $attempt->autoSubmit();
            return response()->json([
                'success' => true,
                'auto_submit' => true,
                'message' => 'Ujian otomatis diselesaikan karena terlalu banyak pelanggaran',
            ]);
        }

        return response()->json([
            'success' => true,
            'violations' => $attempt->tab_switches + $attempt->fullscreen_exits,
        ]);
    }
}
