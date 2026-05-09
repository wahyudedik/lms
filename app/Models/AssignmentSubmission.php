<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'score',
        'final_score',
        'feedback',
        'penalty_applied',
        'revision_count',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'file_size' => 'integer',
            'score' => 'integer',
            'final_score' => 'decimal:2',
            'penalty_applied' => 'integer',
            'revision_count' => 'integer',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the assignment that this submission belongs to.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the user (student) who made this submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user (guru/dosen) who graded this submission.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Helpers
     */

    /**
     * Check if this submission has been graded.
     */
    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    /**
     * Check if this submission can be revised.
     * A submission can be revised if it has not been graded AND
     * the assignment can still accept submissions (deadline not passed OR late_policy != reject).
     */
    public function canRevise(): bool
    {
        if ($this->isGraded()) {
            return false;
        }

        return $this->assignment->canAcceptSubmission();
    }

    /**
     * Get the URL for the submitted file.
     */
    public function getFileUrl(): ?string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file size formatted in a human-readable format.
     */
    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }
}
