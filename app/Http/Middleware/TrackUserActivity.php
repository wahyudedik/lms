<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // Only track for authenticated users
        if (auth()->check()) {
            $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            $this->logActivity($request, $duration);
        }

        return $response;
    }

    /**
     * Log the user activity
     */
    protected function logActivity(Request $request, float $duration): void
    {
        $user = auth()->user();
        $route = $request->route();

        // Skip certain routes (API, assets, etc.)
        if ($this->shouldSkipRoute($request)) {
            return;
        }

        // Determine activity type
        $activityType = $this->getActivityType($request);
        $activityName = $this->getActivityName($request);

        // Log the activity
        UserActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'activity_name' => $activityName,
            'description' => $this->getActivityDescription($request),
            'metadata' => [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route_name' => $route?->getName(),
                'params' => $route?->parameters() ?? [],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'duration_seconds' => round($duration / 1000), // Convert back to seconds
        ]);
    }

    /**
     * Determine if route should be skipped
     */
    protected function shouldSkipRoute(Request $request): bool
    {
        $skipPatterns = [
            'api/*',
            'livewire/*',
            '_debugbar/*',
            'horizon/*',
            'telescope/*',
            '*.js',
            '*.css',
            '*.jpg',
            '*.png',
            '*.gif',
            '*.svg',
            '*.woff',
            '*.ttf',
        ];

        foreach ($skipPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get activity type from request
     */
    protected function getActivityType(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 'page_view';
        }

        // Map route names to activity types
        if (str_contains($routeName, 'exam') && str_contains($routeName, 'take')) {
            return 'exam_taking';
        }

        if (str_contains($routeName, 'exam') && str_contains($routeName, 'submit')) {
            return 'exam_submit';
        }

        if (str_contains($routeName, 'material') && str_contains($routeName, 'show')) {
            return 'material_view';
        }

        if (str_contains($routeName, 'course') && str_contains($routeName, 'show')) {
            return 'course_view';
        }

        if (str_contains($routeName, 'analytics')) {
            return 'analytics_view';
        }

        if (str_contains($routeName, 'dashboard')) {
            return 'dashboard_view';
        }

        return 'page_view';
    }

    /**
     * Get activity name from request
     */
    protected function getActivityName(Request $request): string
    {
        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return 'Unknown Page';
        }

        // Get readable name from route
        $name = str_replace('.', ' ', $routeName);
        $name = ucwords($name);

        return $name;
    }

    /**
     * Get activity description
     */
    protected function getActivityDescription(Request $request): string
    {
        $user = auth()->user();
        $activityName = $this->getActivityName($request);

        return "{$user->name} accessed {$activityName}";
    }
}
