<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $status
 * @property int $progress
 * @property \Illuminate\Support\Carbon $enrolled_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Certificate|null $certificate
 * @property-read \App\Models\Course $course
 * @property-read string $progress_color
 * @property-read string $status_color
 * @property-read string $status_display
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment byCourse($courseId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment byStudent($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment completed()
 * @method static \Database\Factories\EnrollmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereEnrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enrollment whereUserId($value)
 * @mixin \Eloquent
 */
class Enrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress',
        'enrolled_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    /**
     * Get the student (user) for this enrollment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student (alias for user)
     */
    public function student()
    {
        return $this->user();
    }

    /**
     * Get the course for this enrollment
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the certificate for this enrollment
     */
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * Check if enrollment has certificate
     */
    public function hasCertificate(): bool
    {
        return $this->certificate()->exists();
    }

    /**
     * Check if enrollment is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if enrollment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark enrollment as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
        ]);
    }

    /**
     * Drop the enrollment
     */
    public function drop(): bool
    {
        return $this->update(['status' => 'dropped']);
    }

    /**
     * Update progress percentage
     */
    public function updateProgress(int $progress): bool
    {
        $progress = max(0, min(100, $progress)); // Ensure 0-100

        $data = ['progress' => $progress];

        // Auto-complete if progress reaches 100
        if ($progress === 100 && $this->status === 'active') {
            $data['status'] = 'completed';
            $data['completed_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * Scope to get only active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get enrollments by course
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to get enrollments by student
     */
    public function scopeByStudent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'dropped' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'dropped' => 'Berhenti',
            default => 'Unknown'
        };
    }

    /**
     * Get progress bar color based on percentage
     */
    public function getProgressColorAttribute(): string
    {
        return match (true) {
            $this->progress >= 80 => 'green',
            $this->progress >= 50 => 'yellow',
            $this->progress >= 25 => 'orange',
            default => 'red'
        };
    }
}
