<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['instructor', 'enrollments']);

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

        // Filter by instructor
        if ($request->has('instructor_id') && $request->instructor_id != '') {
            $query->where('instructor_id', $request->instructor_id);
        }

        $courses = $query->latest()->paginate(10)->withQueryString();
        $instructors = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'instructors'));
    }

    /**
     * Show the form for creating a new course
     */
    public function create()
    {
        $instructors = User::where('role', 'guru')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.courses.create', compact('instructors'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:courses,code',
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,published,archived',
            'max_students' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
        ]);

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
            ->route('admin.courses.show', $course)
            ->with('success', 'Kelas berhasil dibuat!');
    }

    /**
     * Display the specified course
     */
    public function show(Course $course)
    {
        $course->load(['instructor', 'enrollments.student']);
        $activeStudents = $course->enrollments()->where('status', 'active')->count();
        $completedStudents = $course->enrollments()->where('status', 'completed')->count();

        return view('admin.courses.show', compact('course', 'activeStudents', 'completedStudents'));
    }

    /**
     * Show the form for editing the specified course
     */
    public function edit(Course $course)
    {
        $instructors = User::where('role', 'guru')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    /**
     * Update the specified course
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'instructor_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,published,archived',
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
            ->route('admin.courses.show', $course)
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified course
     */
    public function destroy(Course $course)
    {
        // Delete cover image if exists
        if ($course->cover_image && Storage::disk('public')->exists($course->cover_image)) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }

    /**
     * Toggle course status (publish/archive)
     */
    public function toggleStatus(Course $course)
    {
        if ($course->status === 'published') {
            $course->archive();
            $message = 'Kelas berhasil diarsipkan!';
        } else {
            $course->publish();
            $message = 'Kelas berhasil dipublikasikan!';
        }

        return back()->with('success', $message);
    }
}
