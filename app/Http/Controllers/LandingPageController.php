<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Display the landing page for the active school
     */
    public function index(Request $request)
    {
        // Get active school with cached query
        $school = School::getActiveLandingSchool();

        // If no active school, create a default object to prevent null errors in the view
        if (!$school) {
            $school = new School([
                'name' => config('app.name', 'LMS'),
                'meta_title' => config('app.name', 'LMS'),
                'meta_description' => 'Learning Management System',
                'contact_phone' => '',
                'contact_email' => '',
                'contact_address' => '',
            ]);
        }

        // Load published courses from instructors belonging to this school
        if ($school->exists) {
            $school->setRelation('courses', \App\Models\Course::whereHas('instructor', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->where('status', 'published')
                ->with('instructor')
                ->latest()
                ->limit(6)
                ->get());
        } else {
            $school->setRelation('courses', collect());
        }

        // Track visit (non-blocking)
        $this->trackVisit($request, $school->exists ? $school : null);

        // Return view with school data
        return view('landing', compact('school'));
    }

    /**
     * Track landing page visit for analytics
     * Only logs for authenticated users (UserActivityLog requires user_id)
     */
    protected function trackVisit(Request $request, ?School $school): void
    {
        // Only track authenticated users - guest visits are not logged
        // to avoid foreign key constraint issues with user_id
        if (!auth()->check()) {
            return;
        }

        try {
            UserActivityLog::create([
                'user_id' => auth()->id(),
                'activity_type' => 'page_view',
                'activity_name' => 'viewed_landing_page',
                'description' => 'Visited landing page for ' . ($school?->name ?? 'unknown school'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'referer' => $request->header('referer'),
                    'school_id' => $school?->id,
                    'school_name' => $school?->name,
                ],
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break page load for analytics
            logger()->error('Failed to track landing page visit', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
