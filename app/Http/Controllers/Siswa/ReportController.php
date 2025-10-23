<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display student's grades dashboard
     */
    public function index(Request $request)
    {
        $attempts = ExamAttempt::with(['exam.course'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'in_progress');

        // Filter by course
        if ($request->filled('course_id')) {
            $attempts->whereHas('exam', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $attempts = $attempts->orderBy('submitted_at', 'desc')->get();

        // Get courses for filter
        $enrolledCourses = auth()->user()->enrolledCourses()
            ->where('enrollments.status', 'active')
            ->get();

        // Calculate overall statistics
        $gradedAttempts = $attempts->where('status', 'graded');
        $statistics = [
            'total_exams' => $attempts->count(),
            'completed' => $gradedAttempts->count(),
            'average_score' => $gradedAttempts->avg('score'),
            'highest_score' => $gradedAttempts->max('score'),
            'lowest_score' => $gradedAttempts->min('score'),
            'pass_count' => $gradedAttempts->where('passed', true)->count(),
            'fail_count' => $gradedAttempts->where('passed', false)->count(),
            'pass_rate' => $gradedAttempts->count() > 0
                ? ($gradedAttempts->where('passed', true)->count() / $gradedAttempts->count() * 100)
                : 0,
        ];

        // Group by course
        $attemptsByCourse = $attempts->groupBy('exam.course.id')->map(function ($courseAttempts) {
            $graded = $courseAttempts->where('status', 'graded');
            return [
                'course' => $courseAttempts->first()->exam->course,
                'total' => $courseAttempts->count(),
                'average' => $graded->avg('score'),
                'passed' => $graded->where('passed', true)->count(),
            ];
        });

        return view('siswa.reports.index', compact(
            'attempts',
            'statistics',
            'attemptsByCourse',
            'enrolledCourses'
        ));
    }

    /**
     * Export transcript to Excel
     */
    public function exportExcel(Request $request)
    {
        $attempts = ExamAttempt::with(['exam.course'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'in_progress');

        if ($request->filled('course_id')) {
            $attempts->whereHas('exam', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $attempts = $attempts->orderBy('submitted_at', 'desc')->get();

        $filename = 'transkrip_' . auth()->user()->name . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(
            new \App\Exports\StudentTranscriptExport($attempts, auth()->user()),
            $filename
        );
    }

    /**
     * Export transcript to PDF
     */
    public function exportPdf(Request $request)
    {
        $attempts = ExamAttempt::with(['exam.course'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'in_progress');

        if ($request->filled('course_id')) {
            $attempts->whereHas('exam', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $attempts = $attempts->orderBy('submitted_at', 'desc')->get();

        // Calculate statistics
        $gradedAttempts = $attempts->where('status', 'graded');
        $statistics = [
            'total_exams' => $attempts->count(),
            'completed' => $gradedAttempts->count(),
            'average_score' => $gradedAttempts->avg('score'),
            'highest_score' => $gradedAttempts->max('score'),
            'lowest_score' => $gradedAttempts->min('score'),
            'pass_count' => $gradedAttempts->where('passed', true)->count(),
            'fail_count' => $gradedAttempts->where('passed', false)->count(),
        ];

        $filename = 'transkrip_' . auth()->user()->name . '_' . date('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('siswa.reports.pdf', [
            'student' => auth()->user(),
            'attempts' => $attempts,
            'statistics' => $statistics,
            'generated_at' => now(),
        ]);

        return $pdf->download($filename);
    }
}
