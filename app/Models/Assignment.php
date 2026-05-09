<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The supported file types for assignment submissions.
     */
    public const SUPPORTED_FILE_TYPES = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'mov', 'avi'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'course_id',
        'material_id',
        'created_by',
        'title',
        'description',
        'deadline',
        'max_score',
        'allowed_file_types',
        'late_policy',
        'penalty_percentage',
        'is_published',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'allowed_file_types' => 'array',
            'max_score' => 'integer',
            'penalty_percentage' => 'integer',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the course that owns the assignment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the material linked to the assignment.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the user who created the assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the submissions for the assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Scopes
     */

    /**
     * Scope to get only published assignments.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get assignments by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Helpers
     */

    /**
     * Check if the assignment deadline has passed.
     */
    public function isDeadlinePassed(): bool
    {
        return now()->gt($this->deadline);
    }

    /**
     * Get the allowed file types for this assignment.
     * Returns the configured types or all supported types if none specified.
     */
    public function getAllowedFileTypes(): array
    {
        return $this->allowed_file_types ?? self::SUPPORTED_FILE_TYPES;
    }

    /**
     * Check if the assignment can still accept submissions.
     * Returns true if deadline not passed, or if deadline passed but late_policy is not 'reject'.
     */
    public function canAcceptSubmission(): bool
    {
        if (!$this->isDeadlinePassed()) {
            return true;
        }

        return $this->late_policy !== 'reject';
    }

    /**
     * Get the submission for a specific user.
     */
    public function getSubmissionForUser(User $user): ?AssignmentSubmission
    {
        return $this->submissions()->where('user_id', $user->id)->first();
    }

    /**
     * Get the remaining time until deadline in seconds.
     * Returns null if the deadline has passed.
     */
    public function getRemainingTime(): ?int
    {
        if ($this->isDeadlinePassed()) {
            return null;
        }

        return (int) now()->diffInSeconds($this->deadline, false);
    }
}
