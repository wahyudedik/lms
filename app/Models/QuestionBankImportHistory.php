<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $file_path
 * @property int|null $file_size
 * @property string $status
 * @property int $total_rows
 * @property int $imported_count
 * @property int $skipped_count
 * @property int $error_count
 * @property array<array-key, mixed>|null $errors
 * @property array<array-key, mixed>|null $summary
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int|null $processing_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_file_size
 * @property-read mixed $status_badge
 * @property-read mixed $success_rate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereErrorCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereErrors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereImportedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereProcessingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereSkippedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionBankImportHistory whereUserId($value)
 * @mixin \Eloquent
 */
class QuestionBankImportHistory extends Model
{
    use HasFactory;

    protected $table = 'question_bank_imports';

    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'file_size',
        'status',
        'total_rows',
        'imported_count',
        'skipped_count',
        'error_count',
        'errors',
        'summary',
        'started_at',
        'completed_at',
        'processing_time',
    ];

    protected $casts = [
        'errors' => 'array',
        'summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
        'total_rows' => 'integer',
        'imported_count' => 'integer',
        'skipped_count' => 'integer',
        'error_count' => 'integer',
        'processing_time' => 'integer',
    ];

    /**
     * Get the user who performed the import
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>',
            'processing' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Processing</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>',
            'failed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_rows == 0) {
            return 0;
        }

        return round(($this->imported_count / $this->total_rows) * 100, 2);
    }

    /**
     * Mark import as processing
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark import as completed
     */
    public function markAsCompleted($stats)
    {
        $this->update([
            'status' => 'completed',
            'imported_count' => $stats['imported'],
            'skipped_count' => $stats['skipped'],
            'error_count' => count($stats['errors']),
            'errors' => $stats['errors'],
            'completed_at' => now(),
            'processing_time' => now()->diffInSeconds($this->started_at),
        ]);
    }

    /**
     * Mark import as failed
     */
    public function markAsFailed($error)
    {
        $this->update([
            'status' => 'failed',
            'errors' => [$error],
            'completed_at' => now(),
            'processing_time' => $this->started_at ? now()->diffInSeconds($this->started_at) : 0,
        ]);
    }
}
