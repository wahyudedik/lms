<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'is_guest',
        'guest_name',
        'guest_email',
        'guest_token',
        'started_at',
        'submitted_at',
        'time_spent',
        'score',
        'total_points_earned',
        'total_points_possible',
        'passed',
        'status',
        'tab_switches',
        'fullscreen_exits',
        'violations',
        'shuffled_question_ids',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'passed' => 'boolean',
        'is_guest' => 'boolean',
        'violations' => 'array',
        'shuffled_question_ids' => 'array',
    ];

    /**
     * Get the exam this attempt belongs to
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user (student) who took this exam
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all answers for this attempt
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'attempt_id');
    }

    /**
     * Start the exam attempt
     */
    public function start(): void
    {
        // ✅ FIX: Add null check for exam relationship
        if (!$this->exam) {
            throw new \Exception('Exam not found for this attempt.');
        }

        $this->started_at = now();
        $this->status = 'in_progress';
        $this->ip_address = request()->ip();
        $this->user_agent = request()->userAgent();

        // Shuffle questions if enabled
        if ($this->exam->shuffle_questions) {
            $questionIds = $this->exam->questions()->pluck('id')->shuffle()->toArray();
            $this->shuffled_question_ids = $questionIds;
        }

        $this->save();

        $this->createPlaceholderAnswers();
    }

    /**
     * Submit the exam attempt
     * ✅ FIX BUG #7 & #8: Use atomic update to prevent race condition and double submission
     */
    public function submit(): void
    {
        // Use atomic update to prevent double submission
        $updated = static::where('id', $this->id)
            ->where('status', 'in_progress')
            ->update([
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);

        if (!$updated) {
            // Already submitted or not in progress
            return;
        }

        // Reload to get updated data
        $this->refresh();

        // Calculate time spent in seconds
        if ($this->started_at && $this->submitted_at) {
            $this->time_spent = $this->started_at->diffInSeconds($this->submitted_at);
            $this->save();
        }

        // Auto-grade if possible
        $this->autoGrade();
    }

    /**
     * Auto-grade the attempt (for MCQ, Matching, and auto-gradable Essay questions)
     */
    public function autoGrade(): void
    {
        // ✅ FIX: Add null check for exam relationship
        if (!$this->exam) {
            throw new \Exception('Exam not found for this attempt.');
        }

        $totalPointsEarned = 0;
        $totalPointsPossible = 0;
        $hasManualEssay = false;

        foreach ($this->answers as $answer) {
            // ✅ FIX: Add null check for question relationship
            $question = $answer->question;
            if (!$question) {
                continue; // Skip if question not found
            }

            $totalPointsPossible += $question->points;

            // Handle Essay questions
            if ($question->type === 'essay') {
                if ($question->needsManualGrading()) {
                    // Manual essay - skip auto-grading
                    $hasManualEssay = true;
                    continue;
                } else {
                    // Auto-grade essay (keyword or similarity)
                    $pointsEarned = $question->calculatePoints($answer->answer ?? '');
                    $isCorrect = $pointsEarned == $question->points;

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'points_earned' => $pointsEarned,
                    ]);

                    $totalPointsEarned += $pointsEarned;
                    continue;
                }
            }

            // Auto-grade MCQ and Matching
            $isCorrect = $question->checkAnswer($answer->answer);
            $pointsEarned = $isCorrect ? $question->points : 0;

            $answer->update([
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);

            $totalPointsEarned += $pointsEarned;
        }

        $this->total_points_earned = $totalPointsEarned;
        $this->total_points_possible = $totalPointsPossible;

        // ✅ FIX: Add division by zero check
        if ($totalPointsPossible > 0) {
            $this->score = ($totalPointsEarned / $totalPointsPossible) * 100;
            $this->passed = $this->score >= $this->exam->pass_score;
        } else {
            // No questions or all questions have 0 points
            $this->score = 0;
            $this->passed = false;
        }

        // If there are no manual essay questions, mark as graded
        if (!$hasManualEssay) {
            $this->status = 'graded';
        }

        $this->save();
    }

    /**
     * Ensure placeholder answers exist for each question.
     */
    protected function createPlaceholderAnswers(): void
    {
        if (!$this->exam) {
            return;
        }

        if ($this->answers()->exists()) {
            return;
        }

        $questions = $this->exam->questions;

        foreach ($questions as $question) {
            $answerData = [
                'attempt_id' => $this->id,
                'question_id' => $question->id,
                'answer' => null,
            ];

            if (
                $this->exam->shuffle_options
                && in_array($question->type, ['mcq_single', 'mcq_multiple'])
            ) {
                $answerData['shuffled_options'] = $question->getShuffledOptions();
            }

            Answer::create($answerData);
        }
    }

    /**
     * Finalize grading (after manual grading of essays)
     */
    public function finalizeGrading(): void
    {
        $totalPointsEarned = $this->answers->sum('points_earned');
        $totalPointsPossible = $this->answers->sum(function ($answer) {
            return $answer->question->points;
        });

        $this->total_points_earned = $totalPointsEarned;
        $this->total_points_possible = $totalPointsPossible;

        if ($totalPointsPossible > 0) {
            $this->score = ($totalPointsEarned / $totalPointsPossible) * 100;
            $this->passed = $this->score >= $this->exam->pass_score;
        }

        $this->status = 'graded';
        $this->save();
    }

    /**
     * Record tab switch violation
     * ✅ FIX BUG #7: Prevent race condition with atomic operations
     */
    public function recordTabSwitch(): bool
    {
        // Refresh to get latest status before checking
        $this->refresh();

        // Check if attempt is still in progress
        if ($this->status !== 'in_progress') {
            return false;
        }

        // Increment tab switches atomically
        $this->increment('tab_switches');

        $violations = $this->violations ?? [];
        $violations[] = [
            'type' => 'tab_switch',
            'timestamp' => now()->toIso8601String(),
        ];

        $this->violations = $violations;
        $this->save();

        // Refresh to get updated tab_switches count
        $this->refresh();

        $autoSubmitted = false;

        // Auto-submit if max tab switches exceeded (use atomic submit)
        if (
            $this->exam->detect_tab_switch
            && $this->tab_switches >= $this->exam->max_tab_switches
            && $this->status === 'in_progress'
        ) {
            $this->submit(); // Submit uses atomic update, so it's safe
            $this->blockUserForCheating('Terdeteksi kecurangan ujian (tab switch berlebih)', [
                'type' => 'tab_switch_threshold',
                'max_tab_switches' => $this->exam->max_tab_switches,
            ]);
            $autoSubmitted = true;
        }

        return $autoSubmitted;
    }

    /**
     * Record fullscreen exit violation
     */
    public function recordFullscreenExit(): void
    {
        $this->increment('fullscreen_exits');

        $violations = $this->violations ?? [];
        $violations[] = [
            'type' => 'fullscreen_exit',
            'timestamp' => now()->toIso8601String(),
        ];

        $this->violations = $violations;
        $this->save();
    }

    /**
     * Block associated user login if cheating is detected.
     */
    public function blockUserForCheating(string $reason, array $details = []): void
    {
        if ($this->is_guest) {
            return;
        }

        $user = $this->user;

        if (!$user) {
            return;
        }

        $incident = CheatingIncident::create([
            'user_id' => $user->id,
            'exam_id' => $this->exam_id,
            'exam_attempt_id' => $this->id,
            'type' => $details['type'] ?? 'anti_cheat',
            'reason' => $reason,
            'details' => array_merge([
                'tab_switches' => $this->tab_switches,
                'fullscreen_exits' => $this->fullscreen_exits,
            ], $details),
            'blocked_at' => now(),
            'status' => 'blocked',
        ]);

        $user->blockLogin($reason);

        if ($incident) {
            $incident->notifyStakeholders();
        }
    }

    /**
     * Get ordered questions for this attempt
     */
    public function getOrderedQuestions()
    {
        // ✅ FIX: Add null check for exam relationship
        if (!$this->exam) {
            return collect([]);
        }

        if ($this->shuffled_question_ids) {
            $questionIds = $this->shuffled_question_ids;
            $questions = $this->exam->questions()->whereIn('id', $questionIds)->get();

            // Sort by the shuffled order
            return collect($questionIds)->map(function ($id) use ($questions) {
                return $questions->firstWhere('id', $id);
            })->filter();
        }

        return $this->exam->questions;
    }

    /**
     * Get time remaining in seconds
     */
    public function getTimeRemaining(): int
    {
        // ✅ FIX: Add null checks
        if (!$this->started_at || $this->status !== 'in_progress' || !$this->exam) {
            return 0;
        }

        $durationInSeconds = $this->exam->duration_minutes * 60;
        $elapsedSeconds = $this->started_at->diffInSeconds(now());

        return max(0, $durationInSeconds - $elapsedSeconds);
    }

    /**
     * Check if time is up
     */
    public function isTimeUp(): bool
    {
        return $this->getTimeRemaining() === 0;
    }

    /**
     * Check if time has expired (alias for hasTimeExpired - used by GuestExamController)
     */
    public function hasTimeExpired(): bool
    {
        // ✅ FIX: Add null checks
        if (!$this->started_at || $this->status !== 'in_progress' || !$this->exam) {
            return false;
        }

        $durationInSeconds = $this->exam->duration_minutes * 60;
        $elapsedSeconds = $this->started_at->diffInSeconds(now());

        return $elapsedSeconds >= $durationInSeconds;
    }

    /**
     * Auto-submit the exam attempt (used by GuestExamController)
     * ✅ Uses atomic submit() method to prevent race conditions
     */
    public function autoSubmit(): void
    {
        $this->submit(); // Use existing submit() method which has atomic update
    }

    /**
     * Calculate score for the attempt (used by GuestExamController)
     * This is an alias for autoGrade() to maintain consistency
     */
    public function calculateScore(): void
    {
        // This should call autoGrade() which already exists
        if ($this->status === 'in_progress') {
            $this->submitted_at = now();
            $this->status = 'submitted';
            
            // Calculate time spent in seconds
            if ($this->started_at) {
                $this->time_spent = $this->started_at->diffInSeconds($this->submitted_at);
            }
            
            $this->save();
        }
        
        $this->autoGrade();
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'in_progress' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-200 text-blue-800">Sedang Mengerjakan</span>',
            'submitted' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-200 text-yellow-800">Menunggu Penilaian</span>',
            'graded' => $this->passed
                ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-200 text-green-800">Lulus</span>'
                : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-200 text-red-800">Tidak Lulus</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-800">Unknown</span>',
        };
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute(): string
    {
        if (!$this->time_spent) {
            return '-';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;

        return sprintf('%d menit %d detik', $minutes, $seconds);
    }
}
