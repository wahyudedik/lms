<?php

namespace App\Models;

use App\Notifications\CheatingIncidentDetected;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $exam_id
 * @property int|null $exam_attempt_id
 * @property string $type
 * @property string|null $reason
 * @property array<array-key, mixed>|null $details
 * @property \Illuminate\Support\Carbon|null $blocked_at
 * @property string $status
 * @property int|null $resolved_by
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property string|null $resolution_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Exam|null $exam
 * @property-read \App\Models\ExamAttempt|null $examAttempt
 * @property-read string $status_badge
 * @property-read \App\Models\User|null $resolver
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident recent(int $days = 7)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident resolved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereBlockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereExamAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CheatingIncident whereUserId($value)
 * @mixin \Eloquent
 */
class CheatingIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'exam_attempt_id',
        'type',
        'reason',
        'details',
        'blocked_at',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'details' => 'array',
        'blocked_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope helpers
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark incident as resolved.
     */
    public function markResolved(?User $resolver = null, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'resolved',
            'resolved_by' => $resolver?->id,
            'resolved_at' => now(),
            'resolution_notes' => $notes ?? $this->resolution_notes,
        ]);
    }

    /**
     * Trigger notifications to relevant stakeholders.
     */
    public function notifyStakeholders(): void
    {
        $recipients = collect();

        // Active admins
        $recipients = $recipients->merge(
            User::where('role', 'admin')->where('is_active', true)->get()
        );

        // Exam instructor
        if ($this->exam?->course?->instructor) {
            $recipients->push($this->exam->course->instructor);
        }

        // Remove duplicates & user themselves
        $recipients = $recipients
            ->filter()
            ->unique('id')
            ->reject(fn ($user) => $this->user_id && $user->id === $this->user_id);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new CheatingIncidentDetected($this));
    }

    /**
     * Helper to provide default summary badges
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'blocked' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Blocked</span>',
            'resolved' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Resolved</span>',
            'reviewing' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Reviewing</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    /**
     * Convert details to a friendly collection for rendering.
     */
    public function detailEntries(): Collection
    {
        return collect($this->details ?? []);
    }
}

