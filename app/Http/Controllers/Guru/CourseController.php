<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of instructor's courses
     */
    public function index(Request $request)
    {
        $query = Course::where('instructor_id', auth()->id())
            ->with(['enrollments']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(10)->withQueryString();

        return view('guru.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $this->authorize('create', Course::class);
        return view('guru.courses.create');
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:courses,code',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'max_students' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Set instructor to current user
        $validated['instructor_id'] = auth()->id();

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $fileName = time() . '_' . $request->file('cover_image')->getClientOriginalName();
            $validated['cover_image'] = $request->file('cover_image')->storeAs('course-covers', $fileName, 'public');
        }

        // Set published_at if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $course = Course::create($validated);

        return redirect()
            ->route('guru.courses.show', $course)
            ->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        // Check authorization using policy
        $this->authorize('view', $course);

        $course->load(['enrollments.student']);
        $activeStudents = $course->enrollments()->where('status', 'active')->count();
        $completedStudents = $course->enrollments()->where('status', 'completed')->count();

        return view('guru.courses.show', compact('course', 'activeStudents', 'completedStudents'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        // Check authorization using policy
        $this->authorize('update', $course);

        return view('guru.courses.edit', compact('course'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        // Check authorization using policy
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'max_students' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($course->cover_image && Storage::disk('public')->exists($course->cover_image)) {
                Storage::disk('public')->delete($course->cover_image);
            }

            $fileName = time() . '_' . $request->file('cover_image')->getClientOriginalName();
            $validated['cover_image'] = $request->file('cover_image')->storeAs('course-covers', $fileName, 'public');
        }

        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $course->status !== 'published') {
            $validated['published_at'] = now();
        }

        $course->update($validated);

        return redirect()
            ->route('guru.courses.show', $course)
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        // Check authorization using policy
        $this->authorize('delete', $course);

        // Delete cover image if exists
        if ($course->cover_image && Storage::disk('public')->exists($course->cover_image)) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()
            ->route('guru.courses.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }

    /**
     * Toggle course status (publish/draft)
     */
    public function toggleStatus(Course $course)
    {
        // Check authorization using policy (update permission)
        $this->authorize('update', $course);

        if ($course->status === 'published') {
            $course->update(['status' => 'draft']);
            $message = 'Kelas berhasil diubah ke draft!';
        } else {
            $course->publish();
            $message = 'Kelas berhasil dipublikasikan!';
        }

        return back()->with('success', $message);
    }
}
