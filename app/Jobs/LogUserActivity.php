<?php

namespace App\Jobs;

use App\Models\UserActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue a user activity log write.
 *
 * Receives the exact payload previously written inline by the
 * TrackUserActivity middleware so behaviour (fields, values) is
 * unchanged — only the DB write moves off the request cycle.
 */
class LogUserActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId,
        public string $activityType,
        public ?string $activityName,
        public ?string $description,
        public array $metadata,
        public ?string $ipAddress,
        public ?string $userAgent,
        public float $durationSeconds,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        UserActivityLog::create([
            'user_id' => $this->userId,
            'activity_type' => $this->activityType,
            'activity_name' => $this->activityName,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'duration_seconds' => $this->durationSeconds,
        ]);
    }
}
