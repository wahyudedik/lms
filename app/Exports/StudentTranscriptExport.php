<?php

namespace App\Exports;

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

    public function __construct($attempts, $student)
    {
        $this->attempts = $attempts;
        $this->student = $student;
    }

    public function collection()
    {
        return $this->attempts;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kelas',
            'Ujian',
            'Tanggal',
            'Waktu Pengerjaan (menit)',
            'Nilai (%)',
            'Poin',
            'Status',
        ];
    }

    public function map($attempt): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $attempt->exam->course->title,
            $attempt->exam->title,
            $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-',
            $attempt->time_spent ? round($attempt->time_spent / 60, 2) : '-',
            $attempt->score ? number_format($attempt->score, 2) : '-',
            $attempt->total_points_earned && $attempt->total_points_possible
                ? number_format($attempt->total_points_earned, 2) . '/' . number_format($attempt->total_points_possible, 2)
                : '-',
            $attempt->passed === true ? 'LULUS' : ($attempt->passed === false ? 'TIDAK LULUS' : 'Belum Dinilai'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Transkrip ' . substr($this->student->name, 0, 20);
    }
}
