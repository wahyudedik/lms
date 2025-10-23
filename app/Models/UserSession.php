<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'login_at',
        'logout_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    /**
     * Get the user that owns the session
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Start a new session
     */
    public static function startSession(int $userId, string $sessionId): self
    {
        $agent = new Agent();

        $deviceType = $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop');
        $browser = $agent->browser();

        return self::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'login_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_type' => $deviceType,
            'browser' => $browser,
        ]);
    }

    /**
     * End the session
     */
    public function endSession(): void
    {
        if ($this->logout_at) {
            return; // Already ended
        }

        $this->update([
            'logout_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->login_at),
        ]);
    }

    /**
     * Get login frequency for a user
     */
    public static function getLoginFrequency(int $userId, int $days = 30): array
    {
        $sessions = self::where('user_id', $userId)
            ->where('login_at', '>=', now()->subDays($days))
            ->orderBy('login_at')
            ->get();

        $frequency = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($days - $i - 1)->format('Y-m-d');
            $frequency[$date] = 0;
        }

        foreach ($sessions as $session) {
            $date = $session->login_at->format('Y-m-d');
            if (isset($frequency[$date])) {
                $frequency[$date]++;
            }
        }

        return $frequency;
    }

    /**
     * Get average session duration
     */
    public static function getAverageSessionDuration(int $userId): float
    {
        return self::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds') ?? 0;
    }

    /**
     * Get total active time
     */
    public static function getTotalActiveTime(int $userId, int $days = 30): int
    {
        return self::where('user_id', $userId)
            ->where('login_at', '>=', now()->subDays($days))
            ->whereNotNull('duration_seconds')
            ->sum('duration_seconds') ?? 0;
    }
}
