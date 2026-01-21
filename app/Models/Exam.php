<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $course_id
 * @property int $created_by
 * @property string $title
 * @property string|null $description
 * @property string|null $instructions
 * @property int $duration_minutes
 * @property \Illuminate\Support\Carbon|null $start_time
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property int $max_attempts
 * @property bool $shuffle_questions
 * @property bool $shuffle_options
 * @property bool $show_results_immediately
 * @property bool $show_correct_answers
 * @property bool $allow_token_access
 * @property string|null $access_token
 * @property bool $require_guest_name
 * @property bool $require_guest_email
 * @property int|null $max_token_uses Null = unlimited
 * @property int $current_token_uses
 * @property numeric $pass_score
 * @property bool $require_fullscreen
 * @property bool $detect_tab_switch
 * @property int $max_tab_switches
 * @property bool $is_published
 * @property bool $offline_enabled
 * @property int $offline_cache_duration Hours
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $creator
 * @property-read string $status_badge
 * @property-read float $total_points
 * @property-read int $total_questions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam active()
 * @method static \Database\Factories\ExamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereAllowTokenAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereCurrentTokenUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDetectTabSwitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxTabSwitches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereMaxTokenUses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereOfflineCacheDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereOfflineEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam wherePassScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireFullscreen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireGuestEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereRequireGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShowCorrectAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShowResultsImmediately($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShuffleOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereShuffleQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Exam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'offline_enabled',
        'offline_cache_duration',
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
        'offline_enabled' => 'boolean',
        'offline_cache_duration' => 'integer',
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
     * Accessor to always present start_time in the app timezone even though it is stored in UTC.
     */
    public function getStartTimeAttribute($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone'));
    }

    /**
     * Accessor to always present end_time in the app timezone even though it is stored in UTC.
     */
    public function getEndTimeAttribute($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone'));
    }

    /**
     * Mutator to ensure start_time is stored in UTC.
     */
    public function setStartTimeAttribute($value): void
    {
        $this->attributes['start_time'] = $this->convertToUtc($value);
    }

    /**
     * Mutator to ensure end_time is stored in UTC.
     */
    public function setEndTimeAttribute($value): void
    {
        $this->attributes['end_time'] = $this->convertToUtc($value);
    }

    protected function convertToUtc($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $date = $value instanceof Carbon
            ? $value->copy()
            : Carbon::parse($value, config('app.timezone'));

        return $date->setTimezone('UTC')->format('Y-m-d H:i:s');
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
