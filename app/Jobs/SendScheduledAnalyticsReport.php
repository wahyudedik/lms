<?php

namespace App\Jobs;

use App\Mail\AnalyticsReportMail;
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Enrollment;
use App\Models\UserActivityLog;
use App\Models\UserSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendScheduledAnalyticsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reportType; // 'daily', 'weekly', 'monthly'
    public $recipients; // array of email addresses

    /**
     * Create a new job instance.
     */
    public function __construct(string $reportType, array $recipients = [])
    {
        $this->reportType = $reportType;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get report data based on type
        $reportData = $this->generateReportData();
        $period = $this->getPeriodString();

        // Generate PDF
        $pdfPath = $this->generatePDF($reportData, $period);

        // Get recipients
        $recipients = $this->getRecipients();

        // Send email to each recipient
        foreach ($recipients as $email) {
            Mail::to($email)->send(
                new AnalyticsReportMail($reportData, $this->reportType, $period, $pdfPath)
            );
        }

        // Clean up PDF after sending
        if ($pdfPath && file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }

    /**
     * Generate report data
     */
    protected function generateReportData(): array
    {
        $days = $this->getDaysCount();
        $startDate = now()->subDays($days);

        // Calculate statistics
        $stats = [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'published')->count(),
            'total_enrollments' => Enrollment::count(),
            'new_enrollments' => Enrollment::where('created_at', '>=', $startDate)->count(),
            'total_exams' => Exam::count(),
            'exam_attempts' => ExamAttempt::where('created_at', '>=', $startDate)->count(),
            'avg_exam_score' => round(ExamAttempt::where('status', 'graded')
                ->where('created_at', '>=', $startDate)
                ->avg('score') ?? 0, 2),
            'total_logins' => UserSession::where('login_at', '>=', $startDate)->count(),
            'avg_session_duration' => round(UserSession::where('login_at', '>=', $startDate)
                ->whereNotNull('duration_seconds')
                ->avg('duration_seconds') ?? 0),
        ];

        // Generate highlights
        $highlights = $this->generateHighlights($stats, $days);

        // Generate trends
        $trends = $this->generateTrends($startDate);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($stats);

        return [
            'stats' => $stats,
            'highlights' => $highlights,
            'trends' => $trends,
            'recommendations' => $recommendations,
            'dashboard_url' => route('admin.analytics.index'),
        ];
    }

    /**
     * Generate highlights
     */
    protected function generateHighlights(array $stats, int $days): array
    {
        $highlights = [];

        if ($stats['new_users'] > 0) {
            $highlights[] = "🎉 {$stats['new_users']} new users joined in the last {$days} days";
        }

        if ($stats['new_enrollments'] > 0) {
            $highlights[] = "📚 {$stats['new_enrollments']} new course enrollments";
        }

        if ($stats['exam_attempts'] > 0) {
            $highlights[] = "✏️ {$stats['exam_attempts']} exams taken with {$stats['avg_exam_score']}% average score";
        }

        if ($stats['total_logins'] > 0) {
            $avgMin = round($stats['avg_session_duration'] / 60);
            $highlights[] = "⏱️ {$stats['total_logins']} user sessions with {$avgMin} minutes average duration";
        }

        return $highlights;
    }

    /**
     * Generate trends
     */
    protected function generateTrends($startDate): array
    {
        $trends = [];

        // User growth trend
        $previousPeriod = $startDate->copy()->subDays($this->getDaysCount());
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousUsers = User::whereBetween('created_at', [$previousPeriod, $startDate])->count();

        if ($previousUsers > 0) {
            $growth = (($currentUsers - $previousUsers) / $previousUsers) * 100;
            $trends['User Growth'] = $growth > 0
                ? "📈 Increased by " . round($growth, 1) . "%"
                : "📉 Decreased by " . abs(round($growth, 1)) . "%";
        }

        // Engagement trend
        $currentLogins = UserSession::where('login_at', '>=', $startDate)->count();
        $previousLogins = UserSession::whereBetween('login_at', [$previousPeriod, $startDate])->count();

        if ($previousLogins > 0) {
            $growth = (($currentLogins - $previousLogins) / $previousLogins) * 100;
            $trends['User Engagement'] = $growth > 0
                ? "📈 Up by " . round($growth, 1) . "%"
                : "📉 Down by " . abs(round($growth, 1)) . "%";
        }

        return $trends;
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(array $stats): array
    {
        $recommendations = [];

        if ($stats['avg_exam_score'] < 70) {
            $recommendations[] = "Consider reviewing course materials or exam difficulty as average score is below 70%";
        }

        if ($stats['new_enrollments'] == 0) {
            $recommendations[] = "No new enrollments in this period. Consider marketing campaigns or new course offerings";
        }

        if ($stats['avg_session_duration'] < 600) { // Less than 10 minutes
            $recommendations[] = "Average session duration is low. Consider improving content engagement";
        }

        if (empty($recommendations)) {
            $recommendations[] = "Great job! All metrics are performing well. Keep up the good work!";
        }

        return $recommendations;
    }

    /**
     * Generate PDF report
     */
    protected function generatePDF(array $reportData, string $period): ?string
    {
        try {
            $pdf = Pdf::loadView('emails.analytics-report-pdf', [
                'reportData' => $reportData,
                'reportType' => $this->reportType,
                'period' => $period,
            ]);

            $filename = 'analytics-report-' . now()->format('Y-m-d-His') . '.pdf';
            $path = storage_path('app/temp/' . $filename);

            // Ensure directory exists
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $pdf->save($path);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recipients
     */
    protected function getRecipients(): array
    {
        if (!empty($this->recipients)) {
            return $this->recipients;
        }

        // Default: send to all admins
        return User::where('role', 'admin')->pluck('email')->toArray();
    }

    /**
     * Get days count based on report type
     */
    protected function getDaysCount(): int
    {
        return match ($this->reportType) {
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            default => 1,
        };
    }

    /**
     * Get period string
     */
    protected function getPeriodString(): string
    {
        $days = $this->getDaysCount();
        $startDate = now()->subDays($days);

        return $startDate->format('M d, Y') . ' - ' . now()->format('M d, Y');
    }
}
