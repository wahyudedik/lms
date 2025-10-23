<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class AnalyticsReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reportData;
    public $reportType;
    public $period;
    public $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(array $reportData, string $reportType, string $period, ?string $pdfPath = null)
    {
        $this->reportData = $reportData;
        $this->reportType = $reportType;
        $this->period = $period;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ucfirst($this->reportType) . ' Analytics Report - ' . $this->period;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.analytics-report',
            with: [
                'reportData' => $this->reportData,
                'reportType' => $this->reportType,
                'period' => $this->period,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            return [
                Attachment::fromPath($this->pdfPath)
                    ->as('analytics-report.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
