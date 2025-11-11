<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    /**
     * Generate certificate for a completed enrollment
     */
    public function generateForEnrollment(Enrollment $enrollment): Certificate
    {
        // Check if enrollment is completed
        if (!$enrollment->isCompleted()) {
            throw new \Exception('Enrollment must be completed to generate certificate');
        }

        // Check if certificate already exists
        $existing = Certificate::where('enrollment_id', $enrollment->id)->first();
        if ($existing) {
            return $existing;
        }

        // Create certificate
        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'student_name' => $enrollment->user->name,
            'course_title' => $enrollment->course->title,
            'course_description' => $enrollment->course->description,
            'issue_date' => now(),
            'completion_date' => $enrollment->completed_at,
            'final_score' => $this->calculateFinalScore($enrollment),
            'grade' => $this->calculateGrade($enrollment),
            'instructor_name' => $enrollment->course->instructor->name ?? 'Unknown',
            'metadata' => $this->generateMetadata($enrollment),
        ]);

        return $certificate;
    }

    /**
     * Generate PDF for certificate
     */
    public function generatePDF(Certificate $certificate, ?string $template = null)
    {
        $data = [
            'certificate' => $certificate,
            'qrCode' => $this->generateQRCode($certificate),
        ];

        // Get template from settings first, then config, then default
        $template = $template
            ?? \App\Models\Setting::get('certificate_template')
            ?? config('certificate.template', 'default');

        // Map template names to view paths
        $templatePath = match ($template) {
            'modern' => 'certificates.templates.modern',
            'elegant' => 'certificates.templates.elegant',
            'minimalist' => 'certificates.templates.minimalist',
            default => 'certificates.template',
        };

        $pdf = Pdf::loadView($templatePath, $data);

        // Get PDF settings from config
        $paper = config('certificate.pdf.paper', 'a4');
        $orientation = config('certificate.pdf.orientation', 'landscape');

        $pdf->setPaper($paper, $orientation);

        return $pdf;
    }

    /**
     * Download certificate PDF
     */
    public function downloadPDF(Certificate $certificate)
    {
        $pdf = $this->generatePDF($certificate);

        $filename = 'certificate-' . $certificate->certificate_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream certificate PDF (view in browser)
     */
    public function streamPDF(Certificate $certificate)
    {
        $pdf = $this->generatePDF($certificate);

        return $pdf->stream();
    }

    /**
     * Save certificate PDF to storage
     */
    public function savePDF(Certificate $certificate): string
    {
        $pdf = $this->generatePDF($certificate);

        $filename = 'certificates/' . $certificate->certificate_number . '.pdf';

        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Verify certificate by number
     */
    public function verify(string $certificateNumber): ?Certificate
    {
        return Certificate::byCertificateNumber($certificateNumber)
            ->valid()
            ->first();
    }

    /**
     * Calculate final score from enrollment
     */
    protected function calculateFinalScore(Enrollment $enrollment): int
    {
        // You can implement your own scoring logic here
        // For now, using progress as score
        return $enrollment->progress;
    }

    /**
     * Calculate grade from score
     */
    protected function calculateGrade(Enrollment $enrollment): string
    {
        $score = $this->calculateFinalScore($enrollment);

        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };
    }

    /**
     * Generate metadata for certificate
     */
    protected function generateMetadata(Enrollment $enrollment): array
    {
        $course = $enrollment->course;

        return [
            'duration_days' => $enrollment->completed_at
                ->diffInDays($enrollment->enrolled_at),
            'course_level' => $course->level ?? 'beginner',
            'course_category' => $course->category->name ?? 'General',
            'total_lessons' => $course->lessons_count ?? 0,
            'total_hours' => $course->duration_hours ?? 0,
        ];
    }

    /**
     * Generate QR code for verification
     */
    protected function generateQRCode(Certificate $certificate): string
    {
        // Simple implementation - you can use a QR code library for better results
        $url = $certificate->verification_url;

        // For now, return the URL
        // You can integrate with libraries like SimpleSoftwareIO/simple-qrcode
        return $url;
    }

    /**
     * Auto-generate certificates for all completed enrollments without certificates
     */
    public function generateMissing(): int
    {
        $enrollments = Enrollment::completed()
            ->whereDoesntHave('certificate')
            ->with(['user', 'course'])
            ->get();

        $count = 0;

        foreach ($enrollments as $enrollment) {
            try {
                $this->generateForEnrollment($enrollment);
                $count++;
            } catch (\Exception $e) {
                // Log error but continue
                logger()->error('Failed to generate certificate for enrollment ' . $enrollment->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Bulk revoke certificates
     */
    public function bulkRevoke(array $certificateIds, string $reason): int
    {
        $count = 0;

        foreach ($certificateIds as $id) {
            $certificate = Certificate::find($id);
            if ($certificate && $certificate->revoke($reason)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get certificate statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Certificate::count(),
            'valid' => Certificate::valid()->count(),
            'revoked' => Certificate::revoked()->count(),
            'recent' => Certificate::recentlyIssued(30)->count(),
            'this_month' => Certificate::whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->count(),
        ];
    }
}
