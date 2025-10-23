<?php

namespace App\Jobs;

use App\Models\QuestionBankImportHistory;
use App\Imports\QuestionBankImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessQuestionBankImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importHistory;
    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(QuestionBankImportHistory $importHistory, $filePath)
    {
        $this->importHistory = $importHistory;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mark as processing
            $this->importHistory->markAsProcessing();

            // Process import
            $import = new QuestionBankImport();
            Excel::import($import, Storage::path($this->filePath));

            // Get stats
            $stats = $import->getStats();

            // Mark as completed
            $this->importHistory->markAsCompleted($stats);

            // Delete temporary file
            Storage::delete($this->filePath);

            Log::info('Question bank import completed', [
                'import_id' => $this->importHistory->id,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            // Mark as failed
            $this->importHistory->markAsFailed($e->getMessage());

            // Log error
            Log::error('Question bank import failed', [
                'import_id' => $this->importHistory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up file
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->importHistory->markAsFailed($exception->getMessage());

        // Clean up file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}
