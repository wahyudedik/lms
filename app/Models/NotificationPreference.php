<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'via_database',
        'via_push',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'via_database' => 'boolean',
        'via_push' => 'boolean',
    ];

    /**
     * Get the user that owns the notification preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the preference for a given user and notification type.
     * Returns the stored record, or a new unsaved instance with defaults
     * (via_database=true, via_push=true) if no record exists.
     */
    public static function getForUser(int $userId, string $type): self
    {
        $preference = static::where('user_id', $userId)
            ->where('notification_type', $type)
            ->first();

        if ($preference) {
            return $preference;
        }

        $default = new static();
        $default->user_id = $userId;
        $default->notification_type = $type;
        $default->via_database = true;
        $default->via_push = true;

        return $default;
    }
}
