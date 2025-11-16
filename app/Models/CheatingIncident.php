<?php

namespace App\Models;

use App\Notifications\CheatingIncidentDetected;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

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

