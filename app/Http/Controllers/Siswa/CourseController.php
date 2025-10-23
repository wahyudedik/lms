<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display published courses (browse)
     */
    public function index(Request $request)
    {
        $query = Course::published()
            ->with(['instructor', 'enrollments']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('instructor', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $courses = $query->latest('published_at')->paginate(12)->withQueryString();

        return view('siswa.courses.index', compact('courses'));
    }

    /**
     * Display enrolled courses (my courses)
     */
    public function myCourses()
    {
        $enrollments = auth()->user()
            ->enrollments()
            ->with(['course.instructor'])
            ->latest()
            ->paginate(12);

        return view('siswa.courses.my-courses', compact('enrollments'));
    }

    /**
     * Show course detail
     */
    public function show(Course $course)
    {
        // Check if course is published
        if (!$course->isPublished()) {
            abort(404, 'Kelas tidak ditemukan.');
        }

        $course->load(['instructor', 'enrollments']);

        // Check if user is already enrolled
        $isEnrolled = $course->isEnrolledBy(auth()->user());
        $enrollment = null;

        if ($isEnrolled) {
            $enrollment = auth()->user()
                ->enrollments()
                ->where('course_id', $course->id)
                ->first();
        }

        $activeStudentsCount = $course->activeEnrollmentsCount();

        return view('siswa.courses.show', compact('course', 'isEnrolled', 'enrollment', 'activeStudentsCount'));
    }

    /**
     * Enroll in a course
     */
    public function enroll(Course $course)
    {
        // Check if course is published
        if (!$course->isPublished()) {
            return back()->with('error', 'Kelas tidak tersedia untuk pendaftaran.');
        }

        // Check if already enrolled
        if ($course->isEnrolledBy(auth()->user())) {
            return back()->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        // Check if course is full
        if ($course->isFull()) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        // Enroll user
        auth()->user()->enrollInCourse($course->id);

        return redirect()
            ->route('siswa.courses.show', $course)
            ->with('success', 'Berhasil mendaftar ke kelas ini!');
    }

    /**
     * Enroll using course code
     */
    public function enrollByCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:courses,code',
        ]);

        $course = Course::where('code', $validated['code'])->first();

        if (!$course) {
            return back()->with('error', 'Kode kelas tidak ditemukan.');
        }

        if (!$course->isPublished()) {
            return back()->with('error', 'Kelas tidak tersedia untuk pendaftaran.');
        }

        if ($course->isEnrolledBy(auth()->user())) {
            return back()->with('info', 'Anda sudah terdaftar di kelas ini.');
        }

        if ($course->isFull()) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        auth()->user()->enrollInCourse($course->id);

        return redirect()
            ->route('siswa.courses.show', $course)
            ->with('success', 'Berhasil mendaftar ke kelas menggunakan kode!');
    }

    /**
     * Unenroll from a course
     */
    public function unenroll(Course $course)
    {
        $enrollment = auth()->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Anda tidak terdaftar di kelas ini.');
        }

        $enrollment->drop();

        return redirect()
            ->route('siswa.courses.my-courses')
            ->with('success', 'Berhasil keluar dari kelas.');
    }
}
