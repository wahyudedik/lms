<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
