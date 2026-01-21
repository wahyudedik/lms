<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

/**
 * @property int $id
 * @property int $user_id
 * @property string $session_id
 * @property \Illuminate\Support\Carbon $login_at
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property int|null $duration_seconds
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $device_type
 * @property string|null $browser
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLogoutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserId($value)
 * @mixin \Eloquent
 */
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
