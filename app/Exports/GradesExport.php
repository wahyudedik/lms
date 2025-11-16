<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $attempts;
    protected $course;
    protected $exam;

    public function __construct($attempts, $course, $exam = null)
    {
        $this->attempts = $attempts;
        $this->course = $course;
        $this->exam = $exam;
    }

    public function collection()
    {
        return $this->attempts;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Email',
            'Ujian',
            'Tanggal',
            'Waktu Pengerjaan (menit)',
            'Nilai (%)',
            'Poin Diperoleh',
            'Total Poin',
            'Status',
            'Keterangan',
        ];
    }

    public function map($attempt): array
    {
        static $no = 0;
        $no++;

        $studentName = $attempt->is_guest
            ? ($attempt->guest_name ?? 'Tamu')
            : optional($attempt->user)->name;

        $studentEmail = $attempt->is_guest
            ? ($attempt->guest_email ?? '-')
            : optional($attempt->user)->email;

        return [
            $no,
            $studentName ?? 'Tidak diketahui',
            $studentEmail ?? '-',
            optional($attempt->exam)->title,
            $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-',
            $attempt->time_spent ? round($attempt->time_spent / 60, 2) : '-',
            $attempt->score ? number_format($attempt->score, 2) : '-',
            $attempt->total_points_earned ? number_format($attempt->total_points_earned, 2) : '-',
            $attempt->total_points_possible ? number_format($attempt->total_points_possible, 2) : '-',
            $attempt->status === 'graded' ? 'Selesai' : 'Belum Dinilai',
            $attempt->passed === true ? 'LULUS' : ($attempt->passed === false ? 'TIDAK LULUS' : '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
            ],
        ];
    }

    public function title(): string
    {
        return $this->exam ? substr($this->exam->title, 0, 31) : substr($this->course->title, 0, 31);
    }
}
