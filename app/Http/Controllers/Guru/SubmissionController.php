<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\AssignmentGradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    use ResolvesRolePrefix;
    public function __construct(
        protected AssignmentGradingService $gradingService
    ) {}

    /**
     * List submissions for an assignment with status filter and pagination.
     */
    public function index(Request $request, Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $query = $assignment->submissions()->with('user');

        // Filter by status
        $status = $request->input('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $submissions = $query->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('guru.assignments.submissions.index', compact('assignment', 'submissions', 'status'));
    }

    /**
     * Show submission detail with file info.
     */
    public function show(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorize('view', $assignment);

        $submission->load(['user', 'grader']);

        return view('guru.assignments.submissions.show', compact('assignment', 'submission'));
    }

    /**
     * Grade a submission with score and optional feedback.
     */
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorize('grade', $assignment);

        $validated = $request->validate([
            'score' => "required|integer|min:0|max:{$assignment->max_score}",
            'feedback' => 'nullable|string',
        ], [
            'score.required' => 'Nilai wajib diisi.',
            'score.integer' => 'Nilai harus berupa bilangan bulat.',
            'score.min' => 'Nilai tidak boleh kurang dari 0.',
            'score.max' => "Nilai tidak boleh melebihi nilai maksimal ({$assignment->max_score}).",
            'feedback.string' => 'Feedback harus berupa teks.',
        ]);

        $this->gradingService->grade(
            $submission,
            $validated['score'],
            $validated['feedback'] ?? null,
            auth()->user()
        );

        return redirect()
            ->to($this->teacherRoute('assignments.submissions.show', [$assignment, $submission]))
            ->with('success', 'Tugas berhasil dinilai!');
    }

    /**
     * Download the submitted file.
     */
    public function download(Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorize('grade', $assignment);

        if (!Storage::exists($submission->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::download($submission->file_path, $submission->file_name);
    }
}
