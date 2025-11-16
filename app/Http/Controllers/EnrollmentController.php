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
        // Check authorization using policy
        $this->authorize('view', $course);

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
        // Check authorization using policy
        $this->authorize('update', $course);
        $this->authorize('create', Enrollment::class);

        $request->validate([
            'user_ids' => ['required_without:user_id', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $userIds = collect($request->input('user_ids', []));

        if ($userIds->isEmpty() && $request->filled('user_id')) {
            $userIds = collect([$request->input('user_id')]);
        }

        $userIds = $userIds->unique()->values();

        if ($userIds->isEmpty()) {
            return back()->with('info', 'Pilih minimal satu siswa untuk didaftarkan.');
        }

        $added = [];
        $skipped = [];
        $classFull = false;

        foreach ($userIds as $userId) {
            $student = User::find($userId);

            if (!$student) {
                $skipped[] = "ID {$userId} tidak ditemukan.";
                continue;
            }

            if (!$student->isSiswa()) {
                $skipped[] = "{$student->name} bukan akun siswa.";
                continue;
            }

            if ($course->isEnrolledBy($student)) {
                $skipped[] = "{$student->name} sudah terdaftar.";
                continue;
            }

            if ($course->isFull()) {
                $classFull = true;
                break;
            }

            $student->enrollInCourse($course->id);
            $added[] = $student->name;
        }

        if (empty($added)) {
            $message = $skipped
                ? 'Tidak ada siswa yang ditambahkan. Alasan: ' . implode(' | ', $skipped)
                : 'Tidak ada siswa yang dapat ditambahkan.';

            return back()->with('info', $message);
        }

        $message = count($added) . ' siswa berhasil ditambahkan.';

        if ($skipped) {
            $message .= ' Terlewati: ' . implode(', ', $skipped);
        }

        if ($classFull) {
            $message .= ' Kelas sudah penuh, sisa siswa tidak dapat ditambahkan.';
        }

        return back()->with('success', $message);
    }

    /**
     * Remove student from course
     */
    public function destroy(Course $course, Enrollment $enrollment)
    {
        // Check authorization using policy
        $this->authorize('delete', $enrollment);

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
        // Check authorization using policy
        $this->authorize('update', $enrollment);

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
        // Check authorization using policy
        $this->authorize('update', $enrollment);

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
