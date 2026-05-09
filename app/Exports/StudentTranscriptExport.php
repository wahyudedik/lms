<?php

namespace App\Exports;

use App\Models\AssignmentSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTranscriptExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $attempts;
    protected $student;
    protected $assignmentSubmissions;

    public function __construct($attempts, $student, $assignmentSubmissions = null)
    {
        $this->attempts = $attempts;
        $this->student = $student;
        $this->assignmentSubmissions = $assignmentSubmissions;
    }

    public function collection()
    {
        // If assignment submissions are provided, merge them with exam attempts
        if ($this->assignmentSubmissions !== null && $this->assignmentSubmissions->isNotEmpty()) {
            $assignmentRows = $this->assignmentSubmissions->map(function ($submission) {
                $submission->_is_assignment = true;

                return $submission;
            });

            return $this->attempts->concat($assignmentRows);
        }

        return $this->attempts;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kelas',
            'Tipe',
            'Judul',
            'Tanggal',
            'Waktu Pengerjaan (menit)',
            'Nilai',
            'Poin',
            'Status',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        // Check if this is an assignment submission
        if ($item instanceof AssignmentSubmission || !empty($item->_is_assignment)) {
            $statusMap = [
                'submitted' => 'Dikumpulkan',
                'late' => 'Terlambat',
                'graded' => 'Dinilai',
            ];

            return [
                $no,
                optional(optional($item->assignment)->course)->title ?? '-',
                'Tugas',
                optional($item->assignment)->title ?? '-',
                $item->submitted_at ? $item->submitted_at->format('d/m/Y H:i') : '-',
                '-',
                $item->final_score !== null ? number_format($item->final_score, 2) : '-',
                $item->score !== null && optional($item->assignment)->max_score
                    ? $item->score . '/' . $item->assignment->max_score
                    : '-',
                $statusMap[$item->status] ?? $item->status,
            ];
        }

        // Exam attempt (original behavior)
        return [
            $no,
            optional(optional($item->exam)->course)->title ?? '-',
            'Ujian',
            optional($item->exam)->title ?? '-',
            $item->submitted_at ? $item->submitted_at->format('d/m/Y H:i') : '-',
            $item->time_spent ? round($item->time_spent / 60, 2) : '-',
            $item->score ? number_format($item->score, 2) . '%' : '-',
            $item->total_points_earned && $item->total_points_possible
                ? number_format($item->total_points_earned, 2) . '/' . number_format($item->total_points_possible, 2)
                : '-',
            $item->passed === true ? 'LULUS' : ($item->passed === false ? 'TIDAK LULUS' : 'Belum Dinilai'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Transkrip ' . substr($this->student->name, 0, 20);
    }
}
