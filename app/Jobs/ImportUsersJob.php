<?php

namespace App\Jobs;

use App\Imports\UsersImport;
use App\Models\UserImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Memproses import user di background queue.
 *
 * Kontrak UsersImport (upsert idempoten) TIDAK diubah — job hanya
 * memanfaatkannya: Excel::import(new UsersImport, path) lalu getStats().
 */
class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Import besar (185+ baris, bcrypt + auto-username) berat; satu kali
     * percobaan cukup — job sudah menandai record sebagai failed bila exception.
     */
    public int $tries = 1;

    /**
     * Waktu kerja maksimal (detik). Harus <= --timeout worker (300 di production).
     */
    public int $timeout = 240;

    /**
     * Create a new job instance.
     */
    public function __construct(public UserImport $userImport)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $record = $this->userImport;

        $record->update(['status' => UserImport::STATUS_PROCESSING]);

        try {
            $disk = Storage::disk('local');

            if (! $disk->exists($record->file_path)) {
                throw new \RuntimeException("File import tidak ditemukan di storage ({$record->file_path}).");
            }

            $absolutePath = $disk->path($record->file_path);

            $import = new UsersImport;
            Excel::import($import, $absolutePath);

            $stats = $import->getStats();

            $detailMessages = array_merge(
                $stats['failure_messages'] ?? [],
                $stats['error_messages'] ?? []
            );

            $record->update([
                'status' => UserImport::STATUS_COMPLETED,
                'total_rows' => ($stats['created'] ?? 0) + ($stats['updated'] ?? 0) + ($stats['skipped'] ?? 0),
                'created_count' => $stats['created'] ?? 0,
                'updated_count' => $stats['updated'] ?? 0,
                'failed_count' => $stats['skipped'] ?? 0,
                'errors' => empty($detailMessages) ? null : $detailMessages,
                'finished_at' => now(),
            ]);

            if (($stats['skipped'] ?? 0) > 0) {
                Log::warning('User import: sebagian baris gagal diimpor', [
                    'user_import_id' => $record->id,
                    'created' => $stats['created'],
                    'updated' => $stats['updated'],
                    'failed' => $stats['skipped'],
                    'details' => $detailMessages,
                ]);
            }

            Log::info('User import selesai', [
                'user_import_id' => $record->id,
                'created' => $stats['created'],
                'updated' => $stats['updated'],
                'failed' => $stats['skipped'],
            ]);
        } catch (\Throwable $e) {
            $this->markFailed($e->getMessage());

            Log::error('User import gagal', [
                'user_import_id' => $record->id,
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->deleteTemporaryFile();
        }
    }

    /**
     * Handle a job failure (mis. worker timeout / job dibunuh).
     */
    public function failed(\Throwable $e): void
    {
        $this->markFailed($e->getMessage());

        $this->deleteTemporaryFile();
    }

    /**
     * Tandai record import sebagai failed dengan pesan error singkat.
     */
    protected function markFailed(string $message): void
    {
        $record = $this->userImport;

        if ($record->status === UserImport::STATUS_FAILED) {
            return; // jangan timpa pesan failure yang sudah ada
        }

        $record->update([
            'status' => UserImport::STATUS_FAILED,
            'errors' => ['Import gagal: '.Str::limit($message, 300)],
            'finished_at' => $record->finished_at ?? now(),
        ]);
    }

    /**
     * Hapus file sementara dari storage non-public (baik selesai maupun gagal).
     */
    protected function deleteTemporaryFile(): void
    {
        $record = $this->userImport;

        if (! $record->file_path) {
            return;
        }

        try {
            $disk = Storage::disk('local');

            if ($disk->exists($record->file_path)) {
                $disk->delete($record->file_path);
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal menghapus file sementara import user', [
                'user_import_id' => $record->id,
                'file_path' => $record->file_path,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
