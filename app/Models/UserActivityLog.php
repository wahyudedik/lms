<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_name',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'duration_seconds',
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the user that owns the activity log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity
     */
    public static function logActivity(
        int $userId,
        string $activityType,
        ?string $activityName = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $duration = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'activity_name' => $activityName,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Get average duration for an activity type
     */
    public static function getAverageDuration(int $userId, string $activityType): float
    {
        return self::where('user_id', $userId)
            ->where('activity_type', $activityType)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds') ?? 0;
    }

    /**
     * Get activity count by type
     */
    public static function getActivityCount(int $userId, string $activityType, $days = 30): int
    {
        return self::where('user_id', $userId)
            ->where('activity_type', $activityType)
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }
}
