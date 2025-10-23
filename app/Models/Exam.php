<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'start_time',
        'end_time',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_results_immediately',
        'show_correct_answers',
        'pass_score',
        'require_fullscreen',
        'detect_tab_switch',
        'max_tab_switches',
        'is_published',
        'published_at',
        'allow_token_access',
        'access_token',
        'require_guest_name',
        'require_guest_email',
        'max_token_uses',
        'current_token_uses',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'published_at' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'require_fullscreen' => 'boolean',
        'detect_tab_switch' => 'boolean',
        'is_published' => 'boolean',
        'allow_token_access' => 'boolean',
        'require_guest_name' => 'boolean',
        'require_guest_email' => 'boolean',
    ];

    /**
     * Get the course this exam belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user (guru) who created this exam
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all questions for this exam
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get all attempts for this exam
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Scope for published exams only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for active exams (within time window)
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_published', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                    ->orWhere('start_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>=', $now);
            });
    }

    /**
     * Check if exam is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        if ($this->start_time && $now->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && $now->gt($this->end_time)) {
            return false;
        }

        return true;
    }

    /**
     * Check if exam has started
     */
    public function hasStarted(): bool
    {
        return $this->start_time ? now()->gte($this->start_time) : true;
    }

    /**
     * Check if exam has ended
     */
    public function hasEnded(): bool
    {
        return $this->end_time ? now()->gt($this->end_time) : false;
    }

    /**
     * Get total points possible for this exam
     */
    public function getTotalPointsAttribute(): float
    {
        return $this->questions->sum('points');
    }

    /**
     * Get total number of questions
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions->count();
    }

    /**
     * Get user's attempt(s) for this exam
     */
    public function getUserAttempts($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's best attempt
     */
    public function getUserBestAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'graded')
            ->orderBy('score', 'desc')
            ->first();
    }

    /**
     * Check if user can take this exam
     */
    public function canUserTake($userId): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $attemptCount = $this->attempts()
            ->where('user_id', $userId)
            ->where('status', '!=', 'in_progress')
            ->count();

        return $attemptCount < $this->max_attempts;
    }

    /**
     * Get user's remaining attempts
     */
    public function getRemainingAttempts($userId): int
    {
        $attemptCount = $this->attempts()
            ->where('user_id', $userId)
            ->where('status', '!=', 'in_progress')
            ->count();

        return max(0, $this->max_attempts - $attemptCount);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_published) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-800">Draft</span>';
        }

        if (!$this->hasStarted()) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-200 text-blue-800">Belum Dimulai</span>';
        }

        if ($this->hasEnded()) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-200 text-red-800">Selesai</span>';
        }

        return '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-200 text-green-800">Aktif</span>';
    }

    /**
     * Generate unique access token
     */
    public function generateAccessToken(): string
    {
        do {
            $token = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (self::where('access_token', $token)->exists());

        $this->access_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Check if token access is available
     */
    public function isTokenAccessAvailable(): bool
    {
        if (!$this->allow_token_access || !$this->access_token) {
            return false;
        }

        // Check if max uses reached
        if ($this->max_token_uses !== null && $this->current_token_uses >= $this->max_token_uses) {
            return false;
        }

        // Check if exam is active
        return $this->isActive();
    }

    /**
     * Increment token usage counter
     */
    public function incrementTokenUses(): void
    {
        $this->increment('current_token_uses');
    }

    /**
     * Reset token usage counter
     */
    public function resetTokenUses(): void
    {
        $this->current_token_uses = 0;
        $this->save();
    }

    /**
     * Get token usage percentage
     */
    public function getTokenUsagePercentage(): float
    {
        if ($this->max_token_uses === null || $this->max_token_uses == 0) {
            return 0;
        }

        return ($this->current_token_uses / $this->max_token_uses) * 100;
    }
}
