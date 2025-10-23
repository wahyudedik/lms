<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Show enrollments for a specific course
     */
    public function index(Course $course)
    {
        // Check authorization
        if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $enrollments = $course->enrollments()
            ->with('student')
            ->latest()
            ->paginate(20);

        $availableStudents = User::where('role', 'siswa')
            ->where('is_active', true)
            ->whereNotIn('id', $course->students->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('enrollments.index', compact('course', 'enrollments', 'availableStudents'));
    }

    /**
     * Enroll a student to a course (manual enrollment)
     */
    public function store(Request $request, Course $course)
    {
        // Check authorization
        if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($validated['user_id']);

        // Check if student role
        if (!$student->isSiswa()) {
            return back()->with('error', 'Hanya siswa yang dapat didaftarkan.');
        }

        // Check if already enrolled
        if ($course->isEnrolledBy($student)) {
            return back()->with('info', 'Siswa sudah terdaftar di kelas ini.');
        }

        // Check if course is full
        if ($course->isFull()) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        // Enroll student
        $student->enrollInCourse($course->id);

        return back()->with('success', 'Siswa berhasil didaftarkan!');
    }

    /**
     * Remove student from course
     */
    public function destroy(Course $course, Enrollment $enrollment)
    {
        // Check authorization
        if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ensure enrollment belongs to the course
        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $enrollment->delete();

        return back()->with('success', 'Siswa berhasil dihapus dari kelas!');
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Course $course, Enrollment $enrollment, Request $request)
    {
        // Check authorization
        if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ensure enrollment belongs to the course
        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,completed,dropped',
        ]);

        $enrollment->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed') {
            $enrollment->update([
                'progress' => 100,
                'completed_at' => now(),
            ]);
        }

        return back()->with('success', 'Status pendaftaran berhasil diperbarui!');
    }

    /**
     * Update enrollment progress
     */
    public function updateProgress(Course $course, Enrollment $enrollment, Request $request)
    {
        // Check authorization
        if (auth()->user()->isGuru() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ensure enrollment belongs to the course
        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $enrollment->updateProgress($validated['progress']);

        return back()->with('success', 'Progress berhasil diperbarui!');
    }
}
