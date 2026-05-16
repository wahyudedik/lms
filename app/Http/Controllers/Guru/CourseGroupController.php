<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseGroupController extends Controller
{
    use ResolvesRolePrefix;

    /**
     * Display a listing of groups for the course.
     */
    public function index(Course $course): View
    {
        $this->authorize('viewAny', [CourseGroup::class, $course]);

        $groups = $course->courseGroups()
            ->with('members')
            ->orderBy('name')
            ->get();

        // Collect all student IDs that are already in ANY group in this course
        $allGroupedStudentIds = $groups->flatMap(fn($g) => $g->members->pluck('id'))->unique()->toArray();

        $enrolledStudents = $course->enrollments()
            ->where('status', 'active')
            ->whereHas('user', fn($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
            ->with('user')
            ->get()
            ->pluck('user')
            ->sortBy('name');

        return view('guru.courses.groups.index', compact('course', 'groups', 'enrolledStudents', 'allGroupedStudentIds'));
    }

    /**
     * Store a newly created group.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('create', [CourseGroup::class, $course]);

        $request->merge(['name' => trim($request->input('name', ''))]);

        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
        ]);

        $name = $request->input('name');

        // Case-insensitive uniqueness check within the course
        $exists = $course->courseGroups()
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['name' => 'Nama kelompok sudah digunakan dalam kursus ini.']);
        }

        $course->courseGroups()->create([
            'name' => $name,
        ]);

        return redirect()
            ->to($this->userRoute('courses.groups.index', $course))
            ->with('success', 'Kelompok berhasil dibuat!');
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, Course $course, CourseGroup $group): RedirectResponse
    {
        $this->authorize('update', $group);

        $request->merge(['name' => trim($request->input('name', ''))]);

        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:255'],
        ]);

        $name = $request->input('name');

        // Case-insensitive uniqueness check within the course, excluding current group
        $exists = $course->courseGroups()
            ->where('id', '!=', $group->id)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['name' => 'Nama kelompok sudah digunakan dalam kursus ini.']);
        }

        $group->update([
            'name' => $name,
        ]);

        return redirect()
            ->to($this->userRoute('courses.groups.index', $course))
            ->with('success', 'Kelompok berhasil diperbarui!');
    }

    /**
     * Remove the specified group.
     */
    public function destroy(Course $course, CourseGroup $group): RedirectResponse
    {
        $this->authorize('delete', $group);

        $group->delete();

        return redirect()
            ->to($this->userRoute('courses.groups.index', $course))
            ->with('success', 'Kelompok berhasil dihapus!');
    }

    /**
     * Add a member to the group.
     */
    public function addMember(Request $request, Course $course, CourseGroup $group): RedirectResponse
    {
        $this->authorize('manageMember', $group);

        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userId = $request->input('user_id');

        // Check if the student has an active enrollment in this course
        $isActivelyEnrolled = Enrollment::where('course_id', $course->id)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();

        if (!$isActivelyEnrolled) {
            return redirect()
                ->back()
                ->withErrors(['user_id' => 'Siswa tidak terdaftar aktif di kursus ini.']);
        }

        // Check if the student is already a member of this group
        $alreadyMember = $group->members()->where('user_id', $userId)->exists();

        if ($alreadyMember) {
            return redirect()
                ->back()
                ->withErrors(['user_id' => 'Siswa sudah menjadi anggota kelompok ini.']);
        }

        // Check if the student is already a member of another group in this course (exclusive)
        $inOtherGroup = CourseGroup::where('course_id', $course->id)
            ->where('id', '!=', $group->id)
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->exists();

        if ($inOtherGroup) {
            return redirect()
                ->back()
                ->withErrors(['user_id' => 'Siswa sudah menjadi anggota kelompok lain di kursus ini.']);
        }

        $group->members()->attach($userId);

        return redirect()
            ->to($this->userRoute('courses.groups.index', $course))
            ->with('success', 'Siswa berhasil ditambahkan ke kelompok!');
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember(Course $course, CourseGroup $group, User $user): RedirectResponse
    {
        $this->authorize('manageMember', $group);

        $group->members()->detach($user->id);

        return redirect()
            ->to($this->userRoute('courses.groups.index', $course))
            ->with('success', 'Siswa berhasil dihapus dari kelompok!');
    }
}
