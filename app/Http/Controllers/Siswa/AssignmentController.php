<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Concerns\ResolvesRolePrefix;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\FileValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    use ResolvesRolePrefix;
    public function __construct(
        protected FileValidationService $fileValidationService
    ) {}

    /**
     * List all published assignments for enrolled courses with status per assignment.
     */
    public function index()
    {
        $user = Auth::user();

        // Get enrolled course IDs (active enrollments)
        $enrolledCourseIds = $user->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        // Get all published assignments for enrolled courses
        $assignments = Assignment::with(['course'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->published()
            ->orderBy('deadline', 'asc')
            ->get();

        // Get user's submissions for these assignments
        $submissions = AssignmentSubmission::where('user_id', $user->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get()
            ->keyBy('assignment_id');

        // Attach submission status to each assignment
        $assignments->each(function ($assignment) use ($submissions) {
            $assignment->user_submission = $submissions->get($assignment->id);
        });

        return view('siswa.assignments.index', compact('assignments'));
    }

    /**
     * Show assignment detail, own submission status, remaining time, upload form.
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        $assignment->load(['course', 'material']);

        $user = Auth::user();
        $submission = $assignment->getSubmissionForUser($user);
        $remainingTime = $assignment->getRemainingTime();
        $allowedExtensions = $this->fileValidationService->getAllowedExtensions($assignment);
        $maxFileSize = $this->fileValidationService->getMaxFileSize();

        return view('siswa.assignments.show', compact(
            'assignment',
            'submission',
            'remainingTime',
            'allowedExtensions',
            'maxFileSize'
        ));
    }

    /**
     * Upload/revise submission.
     */
    public function submit(Request $request, Assignment $assignment)
    {
        $this->authorize('submit', $assignment);

        $user = Auth::user();

        // Validate file is present
        $request->validate([
            'file' => 'required|file',
        ], [
            'file.required' => 'File tugas wajib diunggah.',
            'file.file' => 'File tidak valid.',
        ]);

        $file = $request->file('file');

        // Validate file type and size via FileValidationService
        $validation = $this->fileValidationService->validate($file, $assignment);

        if (!$validation['valid']) {
            return back()->withErrors(['file' => $validation['error']])->withInput();
        }

        // Check if existing submission exists (for revision)
        $existingSubmission = $assignment->getSubmissionForUser($user);

        // If submission exists and is graded, prevent revision
        if ($existingSubmission && $existingSubmission->isGraded()) {
            return back()->withErrors(['file' => 'Tugas sudah dinilai, tidak dapat direvisi.']);
        }

        // Determine status based on deadline and late_policy
        $status = 'submitted';
        $penaltyApplied = null;

        if ($assignment->isDeadlinePassed()) {
            if ($assignment->late_policy === 'reject') {
                return back()->withErrors(['file' => 'Batas waktu pengumpulan telah berakhir.']);
            }

            $status = 'late';

            if ($assignment->late_policy === 'penalty') {
                $penaltyApplied = $assignment->penalty_percentage;
            }
        }

        // Store file at assignments/{assignment_id}/{user_id}/{original_filename}
        $fileName = $file->getClientOriginalName();
        $storagePath = "assignments/{$assignment->id}/{$user->id}";
        $filePath = $file->storeAs($storagePath, $fileName, 'public');

        try {
            DB::transaction(function () use (
                $assignment,
                $user,
                $existingSubmission,
                $filePath,
                $fileName,
                $file,
                $status,
                $penaltyApplied
            ) {
                if ($existingSubmission) {
                    // Revision: delete old file
                    if (Storage::disk('public')->exists($existingSubmission->file_path)) {
                        Storage::disk('public')->delete($existingSubmission->file_path);
                    }

                    // Update existing submission record
                    $existingSubmission->update([
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_size' => $file->getSize(),
                        'status' => $status,
                        'penalty_applied' => $penaltyApplied,
                        'revision_count' => $existingSubmission->revision_count + 1,
                        'submitted_at' => now(),
                    ]);
                } else {
                    // Create new submission record
                    AssignmentSubmission::create([
                        'assignment_id' => $assignment->id,
                        'user_id' => $user->id,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_size' => $file->getSize(),
                        'status' => $status,
                        'penalty_applied' => $penaltyApplied,
                        'revision_count' => 0,
                        'submitted_at' => now(),
                    ]);
                }
            });
        } catch (\Exception) {
            // If DB transaction fails, delete the uploaded file
            Storage::disk('public')->delete($filePath);

            return back()->withErrors(['file' => 'Gagal mengunggah file. Silakan coba lagi nanti.']);
        }

        // Dispatch AssignmentSubmitted notification to assignment creator
        if (class_exists(\App\Notifications\AssignmentSubmitted::class)) {
            $assignment->load('creator');
            if ($assignment->creator) {
                $assignment->creator->notify(new \App\Notifications\AssignmentSubmitted($assignment, $user));
            }
        }

        return redirect()
            ->to($this->studentRoute('assignments.show', $assignment))
            ->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
