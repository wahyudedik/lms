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
            try {
                $themeService = app(ThemeService::class);
                $theme = $themeService->getCurrentTheme();
                
                // Safely get school - handle case when table doesn't exist or user has no school
                $school = null;
                try {
                    $school = auth()->user()->school;
                } catch (\Exception $e) {
                    // Table might not exist or relationship issue - continue without school
                }

                // Share theme with all views
                View::share('currentTheme', $theme);
                View::share('currentSchool', $school);
                View::share('themeService', $themeService);
            } catch (\Exception $e) {
                // If theme service fails, continue without theme
                View::share('currentTheme', null);
                View::share('currentSchool', null);
                View::share('themeService', null);
            }
        }

        return $next($request);
    }
}
