<?php

namespace App\Exceptions;

use App\Models\AuthorizationLog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Log authorization failures
        $this->renderable(function (AuthorizationException $e, Request $request) {
            $this->logAuthorizationFailure($e, $request);
        });
    }

    /**
     * Log authorization failure
     */
    protected function logAuthorizationFailure(AuthorizationException $e, Request $request): void
    {
        try {
            $user = $request->user();
            $route = $request->route();

            // Try to extract resource from route parameters
            $resource = null;
            $resourceType = null;
            $resourceId = null;

            if ($route) {
                $parameters = $route->parameters();
                
                // Common resource parameter names
                $resourceParams = ['exam', 'course', 'material', 'question', 'enrollment', 'certificate', 'thread', 'reply'];
                
                foreach ($resourceParams as $param) {
                    if (isset($parameters[$param]) && is_object($parameters[$param])) {
                        $resource = $parameters[$param];
                        $resourceType = get_class($resource);
                        $resourceId = $resource->id;
                        break;
                    }
                }
            }

            // Extract action from route name or method
            $action = $this->extractAction($request);
            $ability = $this->extractAbility($e, $request);

            AuthorizationLog::logDenied(
                $user?->id,
                $resourceType ?? 'unknown',
                $resourceId,
                $action,
                $ability,
                $e->getMessage(),
                [
                    'route_name' => $route?->getName(),
                    'url' => $request->fullUrl(),
                    'exception' => get_class($e),
                ]
            );
        } catch (\Exception $logException) {
            // Silently fail if logging fails to prevent breaking the app
            \Log::error('Failed to log authorization failure', [
                'error' => $logException->getMessage(),
            ]);
        }
    }

    /**
     * Extract action from request
     */
    protected function extractAction(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';
        $method = strtolower($request->method());

        // Try to extract from route name
        if (preg_match('/\.(create|store|edit|update|destroy|show|index)$/', $routeName, $matches)) {
            return $matches[1];
        }

        // Fallback to HTTP method
        return match ($method) {
            'get' => 'view',
            'post' => 'create',
            'put', 'patch' => 'update',
            'delete' => 'delete',
            default => 'unknown',
        };
    }

    /**
     * Extract ability from exception or request
     */
    protected function extractAbility(AuthorizationException $e, Request $request): string
    {
        // Try to get ability from exception message or route
        $routeName = $request->route()?->getName() ?? '';
        
        // Extract from route name if possible
        if (preg_match('/\.(create|store|edit|update|destroy|show|index)$/', $routeName, $matches)) {
            return match ($matches[1]) {
                'create', 'store' => 'create',
                'edit', 'update' => 'update',
                'destroy' => 'delete',
                'show' => 'view',
                'index' => 'viewAny',
                default => 'unknown',
            };
        }

        return 'unknown';
    }
}

