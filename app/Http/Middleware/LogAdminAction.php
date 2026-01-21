<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAction
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $method = strtoupper($request->method());
            $routeName = $request->route()?->getName();

            if ($routeName && Str::startsWith($routeName, 'admin.') && !in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
                $payload = $this->sanitizePayload($request->except([
                    '_token',
                    'password',
                    'password_confirmation',
                    'current_password',
                    'token',
                ]));

                UserActivityLog::logActivity(
                    $user->id,
                    'admin_action',
                    $routeName,
                    'Admin action',
                    [
                        'method' => $method,
                        'route' => $routeName,
                        'url' => $request->fullUrl(),
                        'params' => $request->route()?->parameters() ?? [],
                        'input' => $payload,
                    ]
                );
            }
        }

        return $response;
    }

    protected function sanitizePayload(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if ($value instanceof UploadedFile) {
                $payload[$key] = [
                    'name' => $value->getClientOriginalName(),
                    'size' => $value->getSize(),
                    'mime' => $value->getMimeType(),
                ];
                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
            }
        }

        return $payload;
    }
}
