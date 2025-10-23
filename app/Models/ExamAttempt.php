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
    }

    /**
     * Submit the exam attempt
     */
    public function submit(): void
    {
        $this->submitted_at = now();
        $this->status = 'submitted';

        // Calculate time spent in seconds
        if ($this->started_at) {
            $this->time_spent = $this->started_at->diffInSeconds($this->submitted_at);
        }

        $this->save();

        // Auto-grade if possible
        $this->autoGrade();
    }

    /**
     * Auto-grade the attempt (for MCQ, Matching, and auto-gradable Essay questions)
     */
    public function autoGrade(): void
    {
        $totalPointsEarned = 0;
        $totalPointsPossible = 0;
        $hasManualEssay = false;

        foreach ($this->answers as $answer) {
            $question = $answer->question;
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

        // Calculate score percentage
        if ($totalPointsPossible > 0) {
            $this->score = ($totalPointsEarned / $totalPointsPossible) * 100;
            $this->passed = $this->score >= $this->exam->pass_score;
        }

        // If there are no manual essay questions, mark as graded
        if (!$hasManualEssay) {
            $this->status = 'graded';
        }

        $this->save();
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
     */
    public function recordTabSwitch(): void
    {
        $this->increment('tab_switches');

        $violations = $this->violations ?? [];
        $violations[] = [
            'type' => 'tab_switch',
            'timestamp' => now()->toIso8601String(),
        ];

        $this->violations = $violations;
        $this->save();

        // Auto-submit if max tab switches exceeded
        if (
            $this->exam->detect_tab_switch
            && $this->tab_switches >= $this->exam->max_tab_switches
            && $this->status === 'in_progress'
        ) {
            $this->submit();
        }
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
     * Get ordered questions for this attempt
     */
    public function getOrderedQuestions()
    {
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
        if (!$this->started_at || $this->status !== 'in_progress') {
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
