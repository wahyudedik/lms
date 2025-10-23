<?php

namespace App\Http\Middleware;

use App\Services\ThemeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class LoadSchoolTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $themeService = app(ThemeService::class);
            $theme = $themeService->getCurrentTheme();
            $school = auth()->user()->school;

            // Share theme with all views
            View::share('currentTheme', $theme);
            View::share('currentSchool', $school);
            View::share('themeService', $themeService);
        }

        return $next($request);
    }
}
