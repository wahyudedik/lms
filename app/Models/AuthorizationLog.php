<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $resource_type
 * @property int|null $resource_id
 * @property string $action
 * @property string $ability
 * @property string $result
 * @property string|null $reason
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $route
 * @property string|null $method
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $resource
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog denied()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog forResource(string $resourceType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereAbility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthorizationLog whereUserId($value)
 * @mixin \Eloquent
 */
class AuthorizationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'action',
        'ability',
        'result',
        'reason',
        'ip_address',
        'user_agent',
        'route',
        'method',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that attempted the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the resource that was accessed
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource', 'resource_type', 'resource_id');
    }

    /**
     * Scope: Only denied attempts
     */
    public function scopeDenied($query)
    {
        return $query->where('result', 'denied');
    }

    /**
     * Scope: Only allowed attempts
     */
    public function scopeAllowed($query)
    {
        return $query->where('result', 'allowed');
    }

    /**
     * Scope: Filter by resource type
     */
    public function scopeForResource($query, string $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Recent logs
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Log an authorization attempt
     */
    public static function log(
        ?int $userId,
        string $resourceType,
        ?int $resourceId,
        string $action,
        string $ability,
        string $result,
        ?string $reason = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'ability' => $ability,
            'result' => $result,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'route' => request()->route()?->getName(),
            'method' => request()->method(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log a denied authorization attempt
     */
    public static function logDenied(
        ?int $userId,
        string $resourceType,
        ?int $resourceId,
        string $action,
        string $ability,
        ?string $reason = null,
        ?array $metadata = null
    ): self {
        return self::log(
            $userId,
            $resourceType,
            $resourceId,
            $action,
            $ability,
            'denied',
            $reason,
            $metadata
        );
    }

    /**
     * Log an allowed authorization attempt (optional, for audit trail)
     */
    public static function logAllowed(
        ?int $userId,
        string $resourceType,
        ?int $resourceId,
        string $action,
        string $ability,
        ?array $metadata = null
    ): self {
        return self::log(
            $userId,
            $resourceType,
            $resourceId,
            $action,
            $ability,
            'allowed',
            null,
            $metadata
        );
    }

    /**
     * Get statistics for authorization failures
     */
    public static function getFailureStats(int $days = 30): array
    {
        $logs = self::denied()->recent($days)->get();

        return [
            'total_failures' => $logs->count(),
            'by_resource' => $logs->groupBy('resource_type')->map->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map->count(),
            'recent_failures' => $logs->take(10)->values(),
        ];
    }
}

