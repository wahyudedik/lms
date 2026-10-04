<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialAttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected $attendances,
        protected $course,
    ) {}

    public function collection()
    {
        return $this->attendances;
    }

    public function headings(): array
    {
        return [
            'Materi',
            'Tanggal',
            'Nama Siswa',
            'Username',
            'Status',
            'Alasan',
            'Dicatat Oleh',
        ];
    }

    public function map($attendance): array
    {
        return [
            optional($attendance->material)->title ?? '-',
            optional($attendance->attendance_date)->format('Y-m-d') ?? '-',
            optional($attendance->student)->name ?? '-',
            optional($attendance->student)->username ?? '-',
            $attendance->status_display,
            $attendance->alasan ?? '-',
            optional($attendance->recorder)->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Absensi';
    }
}
