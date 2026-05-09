<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGradeWeight;
use App\Services\AssignmentGradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AssignmentController extends Controller
{
    public function __construct(
        protected AssignmentGradingService $gradingService
    ) {}

    /**
     * List assignments for a course with search/filter.
     */
    public function index(Request $request, Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $query = $course->assignments()->with('creator');

        // Search by title
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->input('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $query->where('is_published', false);
        }

        $assignments = $query->withCount('submissions')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.assignments.index', compact('course', 'assignments'));
    }

    /**
     * Show create form with course materials for linking.
     */
    public function create(Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $materials = $course->materials()->get();

        return view('admin.assignments.create', compact('course', 'materials'));
    }

    /**
     * Validate input, create assignment, dispatch notification if published.
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1',
            'late_policy' => 'required|in:allow,reject,penalty',
            'penalty_percentage' => 'required_if:late_policy,penalty|nullable|integer|min:1|max:100',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'in:pdf,doc,docx,ppt,pptx,mp4,mov,avi',
            'material_id' => 'nullable|exists:materials,id',
            'is_published' => 'boolean',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after' => 'Deadline harus di masa depan.',
            'max_score.required' => 'Nilai maksimal wajib diisi.',
            'max_score.min' => 'Nilai maksimal harus bilangan positif.',
            'late_policy.required' => 'Kebijakan keterlambatan wajib dipilih.',
            'late_policy.in' => 'Kebijakan keterlambatan tidak valid.',
            'penalty_percentage.required_if' => 'Persentase penalti wajib diisi jika kebijakan keterlambatan adalah penalty.',
            'penalty_percentage.min' => 'Persentase penalti harus antara 1-100.',
            'penalty_percentage.max' => 'Persentase penalti harus antara 1-100.',
            'allowed_file_types.*.in' => 'Tipe file tidak diizinkan.',
            'material_id.exists' => 'Materi tidak ditemukan.',
        ]);

        // Validate material belongs to the same course
        if (!empty($validated['material_id'])) {
            $material = $course->materials()->find($validated['material_id']);
            if (!$material) {
                return back()->withErrors(['material_id' => 'Materi harus berasal dari kursus yang sama.'])->withInput();
            }
        }

        $validated['course_id'] = $course->id;
        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published');

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        $assignment = Assignment::create($validated);

        // Dispatch notification if published
        if ($assignment->is_published) {
            $this->notifyEnrolledStudents($course, $assignment);
        }

        return redirect()
            ->route('admin.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dibuat!');
    }

    /**
     * Show assignment detail with submission statistics.
     */
    public function show(Course $course, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load(['creator', 'material']);

        $statistics = $this->gradingService->getSubmissionStatistics($assignment);

        return view('admin.assignments.show', compact('course', 'assignment', 'statistics'));
    }

    /**
     * Show edit form.
     */
    public function edit(Course $course, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $materials = $course->materials()->get();

        return view('admin.assignments.edit', compact('course', 'assignment', 'materials'));
    }

    /**
     * Validate and update assignment.
     */
    public function update(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        // Allow past deadline if it was already set and is in the past
        $deadlineRule = 'required|date|after:now';
        if ($assignment->deadline && $assignment->deadline->isPast()) {
            $deadlineRule = 'required|date';
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => $deadlineRule,
            'max_score' => 'required|integer|min:1',
            'late_policy' => 'required|in:allow,reject,penalty',
            'penalty_percentage' => 'required_if:late_policy,penalty|nullable|integer|min:1|max:100',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'in:pdf,doc,docx,ppt,pptx,mp4,mov,avi',
            'material_id' => 'nullable|exists:materials,id',
            'is_published' => 'boolean',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul maksimal 255 karakter.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after' => 'Deadline harus di masa depan.',
            'max_score.required' => 'Nilai maksimal wajib diisi.',
            'max_score.min' => 'Nilai maksimal harus bilangan positif.',
            'late_policy.required' => 'Kebijakan keterlambatan wajib dipilih.',
            'late_policy.in' => 'Kebijakan keterlambatan tidak valid.',
            'penalty_percentage.required_if' => 'Persentase penalti wajib diisi jika kebijakan keterlambatan adalah penalty.',
            'penalty_percentage.min' => 'Persentase penalti harus antara 1-100.',
            'penalty_percentage.max' => 'Persentase penalti harus antara 1-100.',
            'allowed_file_types.*.in' => 'Tipe file tidak diizinkan.',
            'material_id.exists' => 'Materi tidak ditemukan.',
        ]);

        // Validate material belongs to the same course
        if (!empty($validated['material_id'])) {
            $material = $course->materials()->find($validated['material_id']);
            if (!$material) {
                return back()->withErrors(['material_id' => 'Materi harus berasal dari kursus yang sama.'])->withInput();
            }
        }

        $validated['is_published'] = $request->boolean('is_published');

        // Set published_at if being published for the first time
        if ($validated['is_published'] && !$assignment->is_published) {
            $validated['published_at'] = now();
        }

        $assignment->update($validated);

        return redirect()
            ->route('admin.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Soft-delete assignment.
     */
    public function destroy(Course $course, Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return redirect()
            ->route('admin.courses.assignments.index', $course)
            ->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Toggle is_published status, dispatch notification on publish.
     */
    public function toggleStatus(Course $course, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        if ($assignment->is_published) {
            $assignment->update([
                'is_published' => false,
            ]);
            $message = 'Tugas berhasil di-unpublish!';
        } else {
            $assignment->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

            // Notify enrolled students
            $this->notifyEnrolledStudents($course, $assignment);

            $message = 'Tugas berhasil dipublikasikan!';
        }

        return back()->with('success', $message);
    }

    /**
     * Show grade weight configuration form.
     */
    public function gradeWeights(Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $gradeWeight = CourseGradeWeight::getForCourse($course->id);

        return view('admin.assignments.grade-weights', compact('course', 'gradeWeight'));
    }

    /**
     * Validate weights sum to 100, save, recalculate grades.
     */
    public function updateGradeWeights(Request $request, Course $course)
    {
        $this->authorize('create', [Assignment::class, $course]);

        $validated = $request->validate([
            'assignment_weight' => 'required|integer|min:0|max:100',
            'exam_weight' => 'required|integer|min:0|max:100',
        ], [
            'assignment_weight.required' => 'Bobot tugas wajib diisi.',
            'assignment_weight.min' => 'Bobot tugas harus antara 0-100.',
            'assignment_weight.max' => 'Bobot tugas harus antara 0-100.',
            'exam_weight.required' => 'Bobot ujian wajib diisi.',
            'exam_weight.min' => 'Bobot ujian harus antara 0-100.',
            'exam_weight.max' => 'Bobot ujian harus antara 0-100.',
        ]);

        // Validate sum equals 100
        if (($validated['assignment_weight'] + $validated['exam_weight']) !== 100) {
            return back()->withErrors([
                'assignment_weight' => 'Bobot tugas dan ujian harus berjumlah 100.',
            ])->withInput();
        }

        CourseGradeWeight::updateOrCreate(
            ['course_id' => $course->id],
            [
                'assignment_weight' => $validated['assignment_weight'],
                'exam_weight' => $validated['exam_weight'],
            ]
        );

        // Recalculate all student grades for this course
        $this->gradingService->recalculateAllGrades($course);

        return back()->with('success', 'Bobot nilai berhasil diperbarui!');
    }

    /**
     * Notify enrolled students about a published assignment.
     */
    protected function notifyEnrolledStudents(Course $course, Assignment $assignment): void
    {
        $students = $course->enrollments()
            ->where('status', 'active')
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['siswa', 'mahasiswa']))
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        if ($students->isEmpty()) {
            return;
        }

        // Only dispatch if the notification class exists (created in a later task)
        if (class_exists(\App\Notifications\AssignmentPublished::class)) {
            foreach ($students->chunk(100) as $chunk) {
                Notification::send($chunk, new \App\Notifications\AssignmentPublished($assignment));
            }
        }
    }
}
