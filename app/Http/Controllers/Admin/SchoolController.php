<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Display a listing of schools.
     */
    public function index()
    {
        $schools = School::withCount(['users', 'admins', 'teachers', 'students'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('admin.schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:schools',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
            'domain' => 'nullable|string|max:255|unique:schools',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('schools/favicons', 'public');
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $school = School::create($validated);

        // Create default theme for the school
        $school->theme()->create([]);

        return redirect()->route('admin.schools.show', $school)
            ->with('success', 'School created successfully!');
    }

    /**
     * Display the specified school.
     */
    public function show(School $school)
    {
        $school->load(['theme', 'users' => function ($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total_users' => $school->users()->count(),
            'admins' => $school->admins()->count(),
            'teachers' => $school->teachers()->count(),
            'students' => $school->students()->count(),
        ];

        return view('admin.schools.show', compact('school', 'stats'));
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    /**
     * Update the specified school in storage.
     */
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:schools,slug,' . $school->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:512',
            'domain' => 'nullable|string|max:255|unique:schools,domain,' . $school->id,
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon
            if ($school->favicon) {
                Storage::disk('public')->delete($school->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('schools/favicons', 'public');
        }

        $school->update($validated);

        // Clear theme cache
        $this->themeService->clearCache($school->id);

        return redirect()->route('admin.schools.show', $school)
            ->with('success', 'School updated successfully!');
    }

    /**
     * Remove the specified school from storage.
     */
    public function destroy(School $school)
    {
        // Delete logo and favicon
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }
        if ($school->favicon) {
            Storage::disk('public')->delete($school->favicon);
        }

        // Clear cache
        $this->themeService->clearCache($school->id);

        $school->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'School deleted successfully!');
    }

    /**
     * Toggle school active status
     */
    public function toggleActive(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);

        return back()->with('success', 'School status updated!');
    }
}
