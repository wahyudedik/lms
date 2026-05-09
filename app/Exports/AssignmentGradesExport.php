<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssignmentGradesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $submissions;
    protected $course;

    public function __construct($submissions, Course $course)
    {
        $this->submissions = $submissions;
        $this->course = $course;
    }

    public function collection()
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Email',
            'Tugas',
            'Tanggal Pengumpulan',
            'Nilai',
            'Nilai Maksimal',
            'Nilai Akhir (Setelah Penalti)',
            'Status',
        ];
    }

    public function map($submission): array
    {
        static $no = 0;
        $no++;

        $statusMap = [
            'submitted' => 'Dikumpulkan',
            'late' => 'Terlambat',
            'graded' => 'Dinilai',
        ];

        return [
            $no,
            optional($submission->user)->name ?? 'Tidak diketahui',
            optional($submission->user)->email ?? '-',
            optional($submission->assignment)->title ?? '-',
            $submission->submitted_at ? $submission->submitted_at->format('d/m/Y H:i') : '-',
            $submission->score !== null ? $submission->score : '-',
            optional($submission->assignment)->max_score ?? '-',
            $submission->final_score !== null ? number_format($submission->final_score, 2) : '-',
            $statusMap[$submission->status] ?? $submission->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Tugas ' . substr($this->course->title, 0, 25);
    }
}
