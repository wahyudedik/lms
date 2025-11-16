<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display grades dashboard
     */
    public function index(Request $request)
    {
        $courses = auth()->user()->teachingCourses()->with('enrollments')->get();

        $selectedCourse = null;
        $selectedExam = null;
        $attempts = collect();
        $statistics = [];

        if ($request->filled('course_id')) {
            $selectedCourse = Course::where('id', $request->course_id)
                ->where('instructor_id', auth()->id())
                ->first();

            if ($selectedCourse) {
                if ($request->filled('exam_id')) {
                    $selectedExam = Exam::where('id', $request->exam_id)
                        ->where('course_id', $selectedCourse->id)
                        ->first();

                    if ($selectedExam) {
                        $attempts = $selectedExam->attempts()
                            ->with(['user', 'exam'])
                            ->where('status', '!=', 'in_progress')
                            ->latest('submitted_at')
                            ->get();

                        // Calculate statistics
                        $gradedAttempts = $attempts->where('status', 'graded');
                        $statistics = [
                            'total_students' => $attempts->pluck('user_id')->unique()->count(),
                            'total_attempts' => $attempts->count(),
                            'completed' => $gradedAttempts->count(),
                            'average_score' => $gradedAttempts->avg('score'),
                            'highest_score' => $gradedAttempts->max('score'),
                            'lowest_score' => $gradedAttempts->min('score'),
                            'pass_rate' => $gradedAttempts->count() > 0
                                ? ($gradedAttempts->where('passed', true)->count() / $gradedAttempts->count() * 100)
                                : 0,
                        ];
                    }
                } else {
                    // Show all exams for course
                    $attempts = ExamAttempt::whereHas('exam', function ($q) use ($selectedCourse) {
                        $q->where('course_id', $selectedCourse->id);
                    })
                        ->with(['user', 'exam'])
                        ->where('status', '!=', 'in_progress')
                        ->latest('submitted_at')
                        ->get();

                    $gradedAttempts = $attempts->where('status', 'graded');
                    $statistics = [
                        'total_students' => $attempts->pluck('user_id')->unique()->count(),
                        'total_attempts' => $attempts->count(),
                        'completed' => $gradedAttempts->count(),
                        'average_score' => $gradedAttempts->avg('score'),
                        'pass_rate' => $gradedAttempts->count() > 0
                            ? ($gradedAttempts->where('passed', true)->count() / $gradedAttempts->count() * 100)
                            : 0,
                    ];
                }
            }
        }

        $exams = $selectedCourse ? $selectedCourse->exams : collect();

        return view('guru.reports.index', compact(
            'courses',
            'exams',
            'selectedCourse',
            'selectedExam',
            'attempts',
            'statistics'
        ));
    }

    /**
     * Export consolidated grades to Excel (legacy)
     */
    public function exportExcel(Request $request)
    {
        $courseId = $request->input('course_id');
        $examId = $request->input('exam_id');

        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        if ($examId) {
            $exam = Exam::where('id', $examId)
                ->where('course_id', $course->id)
                ->firstOrFail();

            $attempts = $exam->attempts()
                ->with(['user', 'exam'])
                ->where('status', '!=', 'in_progress')
                ->orderBy('submitted_at', 'desc')
                ->get();

            $filename = 'nilai_' . \Str::slug($exam->title) . '_' . date('Y-m-d') . '.xlsx';
        } else {
            $attempts = ExamAttempt::whereHas('exam', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
                ->with(['user', 'exam'])
                ->where('status', '!=', 'in_progress')
                ->orderBy('submitted_at', 'desc')
                ->get();

            $filename = 'nilai_' . \Str::slug($course->title) . '_' . date('Y-m-d') . '.xlsx';
        }

        return Excel::download(
            new \App\Exports\GradesExport($attempts, $course, $examId ? $exam : null),
            $filename
        );
    }

    /**
     * Export consolidated grades to PDF (legacy)
     */
    public function exportPdf(Request $request)
    {
        $courseId = $request->input('course_id');
        $examId = $request->input('exam_id');

        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        if ($examId) {
            $exam = Exam::where('id', $examId)
                ->where('course_id', $course->id)
                ->firstOrFail();

            $attempts = $exam->attempts()
                ->with(['user', 'exam'])
                ->where('status', '!=', 'in_progress')
                ->orderBy('submitted_at', 'desc')
                ->get();

            $title = 'Laporan Nilai: ' . $exam->title;
            $filename = 'nilai_' . \Str::slug($exam->title) . '_' . date('Y-m-d') . '.pdf';
        } else {
            $attempts = ExamAttempt::whereHas('exam', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
                ->with(['user', 'exam'])
                ->where('status', '!=', 'in_progress')
                ->orderBy('submitted_at', 'desc')
                ->get();

            $title = 'Laporan Nilai: ' . $course->title;
            $filename = 'nilai_' . \Str::slug($course->title) . '_' . date('Y-m-d') . '.pdf';
        }

        // Calculate statistics
        $gradedAttempts = $attempts->where('status', 'graded');
        $statistics = [
            'total_students' => $attempts->pluck('user_id')->unique()->count(),
            'total_attempts' => $attempts->count(),
            'completed' => $gradedAttempts->count(),
            'average_score' => $gradedAttempts->avg('score'),
            'highest_score' => $gradedAttempts->max('score'),
            'lowest_score' => $gradedAttempts->min('score'),
            'pass_rate' => $gradedAttempts->count() > 0
                ? ($gradedAttempts->where('passed', true)->count() / $gradedAttempts->count() * 100)
                : 0,
        ];

        $pdf = Pdf::loadView('guru.reports.pdf', [
            'title' => $title,
            'course' => $course,
            'exam' => $examId ? $exam : null,
            'attempts' => $attempts,
            'statistics' => $statistics,
            'generated_at' => now(),
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export specific exam grades to Excel
     */
    public function exportGradesExcel(Exam $exam)
    {
        abort_if($exam->course->instructor_id !== auth()->id(), 403, 'Anda tidak memiliki akses ke ujian ini.');

        $attempts = $exam->attempts()
            ->with(['user', 'exam'])
            ->where('status', '!=', 'in_progress')
            ->orderByDesc('submitted_at')
            ->get();

        $filename = sprintf(
            'nilai_%s_%s.xlsx',
            \Str::slug($exam->title),
            now()->format('Ymd_His')
        );

        return Excel::download(
            new \App\Exports\GradesExport($attempts, $exam->course, $exam),
            $filename
        );
    }

    /**
     * Export specific exam grades to PDF
     */
    public function exportGradesPdf(Exam $exam)
    {
        abort_if($exam->course->instructor_id !== auth()->id(), 403, 'Anda tidak memiliki akses ke ujian ini.');

        $attempts = $exam->attempts()
            ->with(['user', 'exam'])
            ->where('status', '!=', 'in_progress')
            ->orderByDesc('submitted_at')
            ->get();

        $gradedAttempts = $attempts->where('status', 'graded');
        $statistics = [
            'total_students' => $attempts->pluck('user_id')->unique()->count(),
            'total_attempts' => $attempts->count(),
            'completed' => $gradedAttempts->count(),
            'average_score' => $gradedAttempts->avg('score'),
            'highest_score' => $gradedAttempts->max('score'),
            'lowest_score' => $gradedAttempts->min('score'),
            'pass_rate' => $gradedAttempts->count() > 0
                ? ($gradedAttempts->where('passed', true)->count() / $gradedAttempts->count() * 100)
                : 0,
        ];

        $pdf = Pdf::loadView('guru.reports.grades_pdf', [
            'exam' => $exam->load('course'),
            'attempts' => $attempts,
            'statistics' => $statistics,
            'generated_at' => now(),
        ]);

        $filename = sprintf(
            'nilai_%s_%s.pdf',
            \Str::slug($exam->title),
            now()->format('Ymd_His')
        );

        return $pdf->download($filename);
    }

    /**
     * Display student detail report
     */
    public function studentDetail(Request $request, $studentId)
    {
        $student = \App\Models\User::findOrFail($studentId);

        // Check if student is enrolled in any of guru's courses
        $enrolledCourseIds = $student->enrollments()
            ->whereHas('course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->pluck('course_id');

        if ($enrolledCourseIds->isEmpty()) {
            abort(403, 'Student not enrolled in your courses.');
        }

        $attempts = ExamAttempt::with(['exam.course'])
            ->where('user_id', $student->id)
            ->whereHas('exam.course', function ($q) {
                $q->where('instructor_id', auth()->id());
            })
            ->where('status', '!=', 'in_progress')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $statistics = [
            'total_exams' => $attempts->count(),
            'completed' => $attempts->where('status', 'graded')->count(),
            'average_score' => $attempts->where('status', 'graded')->avg('score'),
            'highest_score' => $attempts->where('status', 'graded')->max('score'),
            'lowest_score' => $attempts->where('status', 'graded')->min('score'),
            'pass_count' => $attempts->where('passed', true)->count(),
            'fail_count' => $attempts->where('passed', false)->count(),
        ];

        return view('guru.reports.student-detail', compact('student', 'attempts', 'statistics'));
    }

    /**
     * Export a specific student's transcript for a course to PDF
     */
    public function exportStudentTranscriptPdf(Course $course, User $student)
    {
        abort_if($course->instructor_id !== auth()->id(), 403, 'Anda tidak memiliki akses ke kursus ini.');
        abort_unless($course->isEnrolledBy($student), 404, 'Siswa tidak terdaftar di kursus ini.');

        $course->load([
            'instructor',
            'exams' => function ($query) use ($student) {
                $query->with(['attempts' => function ($attempts) use ($student) {
                    $attempts->where('user_id', $student->id)
                        ->where('status', '!=', 'in_progress')
                        ->orderByDesc('submitted_at');
                }]);
            },
        ]);

        $pdf = Pdf::loadView('guru.reports.student_transcript_pdf', [
            'course' => $course,
            'student' => $student,
            'exams' => $course->exams,
        ]);

        $filename = sprintf(
            'transkrip_%s_%s_%s.pdf',
            \Str::slug($student->name),
            \Str::slug($course->title),
            now()->format('Ymd_His')
        );

        return $pdf->download($filename);
    }
}
