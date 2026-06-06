<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ResolvesRolePrefix;
    /**
     * Display published courses (browse)
     */
    public function index(Request $request)
    {
        $query = Course::published()
            ->with(['instructor', 'enrollments']);

        // Search
        if ($request->has('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
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

        // Eager load materials with relationships to avoid N+1 queries in view
        $materials = $course->materials()
            ->published()
            ->visibleToStudent(auth()->user())
            ->ordered()
            ->with(['courseGroups', 'comments.user', 'comments.replies.user'])
            ->get();

        // Eager load assignments with submissions to avoid queries in Blade template
        $assignments = collect();
        if ($isEnrolled) {
            $assignments = $course->assignments()
                ->published()
                ->visibleToStudent(auth()->user())
                ->with(['courseGroups', 'submissions' => function ($query) {
                    $query->where('user_id', auth()->id());
                }])
                ->orderBy('deadline', 'asc')
                ->get();
        }

        return view('siswa.courses.show', compact('course', 'isEnrolled', 'enrollment', 'activeStudentsCount', 'materials', 'assignments'));
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

        // Bug #17: Wrap enrollment in transaction to prevent TOCTOU race condition
        // (concurrent requests could both pass isFull() check and over-enroll)
        $courseFull = false;
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($course, &$courseFull) {
                // Lock the course row to prevent concurrent enrollment checks
                $course->lockForUpdate();

                if ($course->isFull()) {
                    $courseFull = true;
                    return;
                }

                auth()->user()->enrollInCourse($course->id);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate enrollment (unique constraint violation)
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('info', 'Anda sudah terdaftar di kelas ini.');
            }
            throw $e;
        }

        if ($courseFull) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        return redirect()
            ->to($this->studentRoute('courses.show', $course))
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

        // Bug #17: Same TOCTOU race condition fix as enroll()
        $courseFull = false;
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($course, &$courseFull) {
                $course->lockForUpdate();

                if ($course->isFull()) {
                    $courseFull = true;
                    return;
                }

                auth()->user()->enrollInCourse($course->id);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('info', 'Anda sudah terdaftar di kelas ini.');
            }
            throw $e;
        }

        if ($courseFull) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        return redirect()
            ->to($this->studentRoute('courses.show', $course))
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
            ->to($this->studentRoute('courses.my-courses'))
            ->with('success', 'Berhasil keluar dari kelas.');
    }
}
